<?php

namespace App\Http\Livewire\Reporte\Agente;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteAgentesxSolicitudesExport;
use App\Exports\ReporteAgentexSolicitudesHoyExport;
use App\Models\solicitudes;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Procesado extends Component
{
    use AuthorizesRequests;

    public $solicitudes, $agentes;
    public $fecha_desde = null, $fecha_hasta = null, $filtro_ok = false; //Variables para filtrar por rango de fechas
    public $hoy, $mañana; //Filtro diario

    /**
     * Carga las solicitudes procesadas con datos detallados de paciente, EPS y servicio.
     */
    private function cargarSolicitudes($fechaDesde, $fechaHasta)
    {
        $query = solicitudes::join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
            ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
            ->join('sedes', 'pservicios.sede_id', '=', 'sedes.id')
            ->leftJoin('users as paciente', 'solicitudes.pacid', '=', 'paciente.id')
            ->leftJoin('eps', 'paciente.eps', '=', 'eps.id')
            ->leftJoin('tipo_identificacions', 'paciente.tdocumento', '=', 'tipo_identificacions.id')
            ->where('solicitudes.updated_at', '>=', $fechaDesde)
            ->where('solicitudes.updated_at', '<', $fechaHasta)
            ->where('solicitudes.estado', '<>', 'Pendiente');

        $user = Auth::user();
        if (!$user->hasRole('Super Admin')) {
            if ($user->sede_id) {
                $query->where('sedes.id', $user->sede_id);
            }
            if ($user->pservicio_id) {
                $query->where('pservicios.id', $user->pservicio_id);
            }
        }

        return $query->select(
            'solicitudes.id',
            'solicitudes.pacid',
            'solicitudes.espec',
            'solicitudes.estado',
            'solicitudes.usercod',
            'solicitudes.created_at',
            'solicitudes.updated_at',
            'solicitudes.motivo_rechazo',
            'solicitudes.motivo_espera',
            DB::raw('paciente.name as paciente_nombre'),
            DB::raw('paciente.apellido1 as paciente_apellido1'),
            DB::raw('paciente.apellido2 as paciente_apellido2'),
            DB::raw('paciente.ndocumento as paciente_ndocumento'),
            DB::raw('tipo_identificacions.nombre as tipo_doc'),
            DB::raw('eps.nombre as eps_nombre'),
            DB::raw('servicios.servnomb as servicio_nombre')
        )->orderBy('solicitudes.updated_at', 'desc')->get();
    }

    public function mount()
    {
        $this->authorize('admin.reporte.agentes');
        $this->hoy    = Carbon::now()->format('Y-m-d');
        $this->mañana = Carbon::tomorrow()->format('Y-m-d');

        $this->solicitudes = $this->cargarSolicitudes($this->hoy, $this->mañana);

        // Filtrar agentes (Consultores) según rol y sede/servicio del usuario actual
        $user = Auth::user();
        $agentesQuery = User::whereHas('roles', function($q){
            $q->where('name', 'Consultor');
        });
        if (!$user->hasRole('Super Admin')) {
            if ($user->sede_id) {
                $agentesQuery->where('sede_id', $user->sede_id);
            }
            if ($user->pservicio_id) {
                $agentesQuery->where('pservicio_id', $user->pservicio_id);
            }
        }
        $this->agentes = $agentesQuery->orderBy('name','asc')->get(['id','name','apellido1','apellido2']);
    }
    public function render()
    {
        return view('livewire.reporte.agente.procesado');
    }

    public function filtroRangoFecha()
    {
        if(!isset($this->fecha_desde) || !isset($this->fecha_hasta))
        {
            if($this->fecha_desde == null) {
                $this->emit('alertError','Seleccione la fecha desde.');
            } else {
                $this->emit('alertError','Seleccione una fecha hasta.');
            }
        } else {
            $fechaHastaInclusive = Carbon::parse($this->fecha_hasta)->addDay()->format('Y-m-d');
            $this->solicitudes = $this->cargarSolicitudes($this->fecha_desde, $fechaHastaInclusive);
            $this->filtro_ok = true;
            $this->emit('alertSuccess','Filtro aplicado correctamente. Se encontraron ' . $this->solicitudes->count() . ' solicitudes.');
        }
    }

    public function exportar()
    {
        $this->authorize('admin.reporte.agentes.exportar.filtro_fecha');
        $fechaHastaInclusive = Carbon::parse($this->fecha_hasta)->addDay()->format('Y-m-d');
        return (new ReporteAgentesxSolicitudesExport($this->fecha_desde, $fechaHastaInclusive))->download('reporte_agentes_desde_'.$this->fecha_desde.'_hasta_'.$this->fecha_hasta.'_.xlsx');
    }

    public function exportarHoy()
    {
        $this->authorize('admin.reporte.agentes.exportar.hoy');
        return (new ReporteAgentexSolicitudesHoyExport($this->hoy,$this->mañana))->download('reporte_'.now().'.xlsx'); //Exporta el reporte del método render.
    }
}
