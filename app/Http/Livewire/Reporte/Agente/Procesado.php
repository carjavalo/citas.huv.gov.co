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

class Procesado extends Component
{
    use AuthorizesRequests;

    public $solicitudes, $agentes;
    public $fecha_desde = null, $fecha_hasta = null, $filtro_ok = false; //Variables para filtrar por rango de fechas
    public $hoy, $mañana; //Filtro diario

    public function mount()
    {
        $this->authorize('admin.reporte.agentes');
        $this->hoy          = Carbon::now()->format('Y-m-d');
        $this->mañana       = Carbon::tomorrow()->format('Y-m-d');
        
        // Consulta base de solicitudes procesadas del día actual
        $query = solicitudes::join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
            ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
            ->join('sedes', 'pservicios.sede_id', '=', 'sedes.id')
            ->where('solicitudes.updated_at','>=', $this->hoy)
            ->where('solicitudes.updated_at','<', $this->mañana)
            ->where('solicitudes.estado','<>','Pendiente');
        
        // Restricción de visibilidad según rol del usuario
        // Super Admin ve todo. Administrador, Coordinador y Consultor filtran por sede y pservicio.
        $user = Auth::user();
        
        if (!$user->hasRole('Super Admin')) {
            if ($user->sede_id) {
                $query->where('sedes.id', $user->sede_id);
            }
            if ($user->pservicio_id) {
                $query->where('pservicios.id', $user->pservicio_id);
            }
        }
        
        $this->solicitudes = $query->select('solicitudes.*')->get();
        
        // Filtrar agentes según rol y sede/servicio del usuario actual
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

    public function filtroRangoFecha() //Esta función se ejecuta cuando el usuario realiza click en "Aplicar filtro"
    {
        if(!isset($this->fecha_desde) || !isset($this->fecha_hasta))//Para evitar error de variable sin inicializar.
        {
            if($this->fecha_desde == null)
            {
                $this->emit('alertError','Seleccione la fecha desde.'); //Evento para emitir alerta
            }else
            {
                $this->emit('alertError','Seleccione una fecha hasta.'); //Evento para emitir alerta
            }
        }else
        {
            // Consulta con filtrado por rol
            $query = solicitudes::join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
                ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
                ->join('sedes', 'pservicios.sede_id', '=', 'sedes.id')
                ->where('solicitudes.updated_at','>=', $this->fecha_desde)
                ->where('solicitudes.updated_at','<', $this->fecha_hasta)
                ->where('solicitudes.estado','<>','PRUEBA');
            
            $user = Auth::user();
            
            if (!$user->hasRole('Super Admin')) {
                if ($user->sede_id) {
                    $query->where('sedes.id', $user->sede_id);
                }
                if ($user->pservicio_id) {
                    $query->where('pservicios.id', $user->pservicio_id);
                }
            }
            
            $this->solicitudes = $query->select('solicitudes.*')->get();
            $this->filtro_ok = true;
            $this->emit('alertSuccess','Filtro aplicado');
        }

    }

    public function exportar()
    {
        $this->authorize('admin.reporte.agentes.exportar.filtro_fecha');
        return (new ReporteAgentesxSolicitudesExport($this->fecha_desde,$this->fecha_hasta))->download('reporte_agentes_desde_'.$this->fecha_desde.'_hasta_'.$this->fecha_hasta.'_.xlsx'); //Exporta reporte con rango de fechas
    }

    public function exportarHoy()
    {
        $this->authorize('admin.reporte.agentes.exportar.hoy');
        return (new ReporteAgentexSolicitudesHoyExport($this->hoy,$this->mañana))->download('reporte_'.now().'.xlsx'); //Exporta el reporte del método render.
    }
}
