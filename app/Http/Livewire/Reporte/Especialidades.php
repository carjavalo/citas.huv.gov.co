<?php

namespace App\Http\Livewire\Reporte;

use Livewire\Component;
use Livewire\WithPagination;
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

    public function cargarTotales()
    {
        $fechaDesde = $this->fechaDesde;
        $fechaHasta = $this->fechaHasta;
        
        // Obtener datos para gráficos (sin paginación)
        $query = servicios::select(
                'servicios.servcod',
                'servicios.servnomb',
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Agendado" THEN 1 ELSE 0 END), 0) as agendadas'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Espera" THEN 1 ELSE 0 END), 0) as en_espera'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Pendiente" THEN 1 ELSE 0 END), 0) as pendientes'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Rechazado" THEN 1 ELSE 0 END), 0) as rechazadas')
            )
            ->leftJoin('solicitudes', function($join) use ($fechaDesde, $fechaHasta) {
                $join->on('servicios.servcod', '=', 'solicitudes.espec')
                     ->whereDate('solicitudes.created_at', '>=', $fechaDesde)
                     ->whereDate('solicitudes.created_at', '<=', $fechaHasta);
            })
            ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
            ->where('servicios.estado', 1);
        
        // Aplicar filtros de visibilidad según rol
        $this->aplicarFiltrosVisibilidad($query);
        
        $this->datosGraficos = $query->groupBy('servicios.servcod', 'servicios.servnomb')
            ->orderBy('servicios.servnomb')
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
        $fechaDesde = $this->fechaDesde;
        $fechaHasta = $this->fechaHasta;
        
        // Actualizar totales en cada render
        $this->cargarTotales();
        
        // Obtener datos paginados para la tabla
        $query = servicios::select(
                'servicios.servcod',
                'servicios.servnomb',
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Agendado" THEN 1 ELSE 0 END), 0) as agendadas'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Espera" THEN 1 ELSE 0 END), 0) as en_espera'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Pendiente" THEN 1 ELSE 0 END), 0) as pendientes'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Rechazado" THEN 1 ELSE 0 END), 0) as rechazadas')
            )
            ->leftJoin('solicitudes', function($join) use ($fechaDesde, $fechaHasta) {
                $join->on('servicios.servcod', '=', 'solicitudes.espec')
                     ->whereDate('solicitudes.created_at', '>=', $fechaDesde)
                     ->whereDate('solicitudes.created_at', '<=', $fechaHasta);
            })
            ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
            ->where('servicios.estado', 1);
        
        // Aplicar filtros de visibilidad según rol
        $this->aplicarFiltrosVisibilidad($query);
        
        $especialidades = $query->groupBy('servicios.servcod', 'servicios.servnomb')
            ->orderBy('servicios.servnomb')
            ->paginate(20);

        return view('livewire.reporte.especialidades', [
            'especialidades' => $especialidades
        ]);
    }

    public function exportarExcel()
    {
        $fecha = Carbon::now()->format('Y-m-d_H-i');
        return Excel::download(
            new EspecialidadesExport($this->fechaDesde, $this->fechaHasta), 
            "reporte_especialidades_{$fecha}.xlsx"
        );
    }
}
