<?php

namespace App\Http\Livewire\Reporte;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\eps;
use App\Models\servicios;
use App\Models\solicitudes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Exports\EspecialidadesExport;
use Maatwebsite\Excel\Facades\Excel;

class Especialidades extends Component
{
    use WithPagination;

    public $fechaDesde;
    public $fechaHasta;
    public $epsId = '';
    public $totalAgendado = 0;
    public $totalEspera = 0;
    public $totalPendiente = 0;
    public $totalRechazado = 0;
    public $datosGraficos = [];

    /**
     * Aplica filtros de visibilidad según rol del usuario
     * Super Admin ve todo. Administrador, Coordinador y Consultor filtran por sede y pservicio.
     */
    private function aplicarFiltrosVisibilidad($query)
    {
        $user = Auth::user();

        if (!$user->hasRole('Super Admin')) {
            if ($user->sede_id) {
                $query->where('pservicios.sede_id', $user->sede_id);
            }
            if ($user->pservicio_id) {
                $query->where('servicios.id_pservicios', $user->pservicio_id);
            }
        }

        return $query;
    }

    /**
     * Consulta agrupada por especialidad con los filtros de fecha, EPS y visibilidad.
     */
    private function consultaEspecialidades()
    {
        $fechaDesde = $this->fechaDesde;
        $fechaHasta = $this->fechaHasta;
        $epsId = $this->epsId;

        $query = servicios::select(
                'servicios.servcod',
                'servicios.servnomb',
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Agendado" THEN 1 ELSE 0 END), 0) as agendadas'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Espera" THEN 1 ELSE 0 END), 0) as en_espera'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Pendiente" THEN 1 ELSE 0 END), 0) as pendientes'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Rechazado" THEN 1 ELSE 0 END), 0) as rechazadas')
            )
            ->leftJoin('solicitudes', function($join) use ($fechaDesde, $fechaHasta, $epsId) {
                $join->on('servicios.servcod', '=', 'solicitudes.espec')
                     ->whereDate('solicitudes.created_at', '>=', $fechaDesde)
                     ->whereDate('solicitudes.created_at', '<=', $fechaHasta);

                // El filtro de EPS va dentro del join para conservar las especialidades sin solicitudes
                if (!empty($epsId)) {
                    $join->whereIn('solicitudes.pacid', function($sub) use ($epsId) {
                        $sub->select('id')->from('users')->where('eps', $epsId);
                    });
                }
            })
            ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
            ->where('servicios.estado', 1);

        // Aplicar filtros de visibilidad según rol
        $this->aplicarFiltrosVisibilidad($query);

        return $query->groupBy('servicios.servcod', 'servicios.servnomb')
            ->orderBy('servicios.servnomb');
    }

    public function mount()
    {
        $this->fechaDesde = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->fechaHasta = Carbon::now()->format('Y-m-d');
        $this->cargarTotales();
    }

    public function updatedFechaDesde()
    {
        $this->resetPage();
        $this->cargarTotales();
    }

    public function updatedFechaHasta()
    {
        $this->resetPage();
        $this->cargarTotales();
    }

    public function updatedEpsId()
    {
        $this->resetPage();
        $this->cargarTotales();
    }

    public function cargarTotales()
    {
        // Obtener datos para gráficos (sin paginación)
        $this->datosGraficos = $this->consultaEspecialidades()
            ->get()
            ->toArray();

        // Calcular totales
        $this->totalAgendado = array_sum(array_column($this->datosGraficos, 'agendadas'));
        $this->totalEspera = array_sum(array_column($this->datosGraficos, 'en_espera'));
        $this->totalPendiente = array_sum(array_column($this->datosGraficos, 'pendientes'));
        $this->totalRechazado = array_sum(array_column($this->datosGraficos, 'rechazadas'));
    }

    public function render()
    {
        // Actualizar totales en cada render
        $this->cargarTotales();

        // Obtener datos paginados para la tabla
        $especialidades = $this->consultaEspecialidades()->paginate(20);

        $listaEps = eps::activas()->orderBy('nombre', 'asc')->get(['id', 'nombre']);
        $epsNombre = optional($listaEps->firstWhere('id', $this->epsId))->nombre;

        return view('livewire.reporte.especialidades', [
            'especialidades' => $especialidades,
            'listaEps' => $listaEps,
            'epsNombre' => $epsNombre,
        ]);
    }

    public function exportarExcel()
    {
        $fecha = Carbon::now()->format('Y-m-d_H-i');
        return Excel::download(
            new EspecialidadesExport($this->fechaDesde, $this->fechaHasta, $this->epsId),
            "reporte_especialidades_{$fecha}.xlsx"
        );
    }
}
