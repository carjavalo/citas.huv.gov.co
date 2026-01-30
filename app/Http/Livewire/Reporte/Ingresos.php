<?php

namespace App\Http\Livewire\Reporte;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserActivity;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActividadesUsuariosExport;

class Ingresos extends Component
{
    use WithPagination;

    public $fechaInicio;
    public $fechaFin;
    public $tipoActividad = '';
    
    public function mount()
    {
        $this->fechaInicio = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->fechaFin = Carbon::now()->format('Y-m-d');
    }

    public function aplicarFiltros()
    {
        $this->resetPage();
    }

    public function exportarExcel()
    {
        $nombreArchivo = 'actividades_usuarios_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(
            new ActividadesUsuariosExport($this->fechaInicio, $this->fechaFin, $this->tipoActividad),
            $nombreArchivo
        );
    }

    public function render()
    {
        // Query base para actividades
        $query = UserActivity::with('user')
            ->whereBetween('created_at', [
                $this->fechaInicio . ' 00:00:00',
                $this->fechaFin . ' 23:59:59'
            ])
            ->whereHas('user', function($q) {
                // Excluir Super Admin
                $q->whereDoesntHave('roles', function($roleQuery) {
                    $roleQuery->where('name', 'Super Admin');
                });
            });

        // Aplicar filtro de tipo si está seleccionado
        if ($this->tipoActividad && $this->tipoActividad !== '') {
            $query->where('tipo_actividad', $this->tipoActividad);
        }

        // Ordenar por fecha descendente y paginar
        $actividades = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estadísticas
        $totalRegistros = UserActivity::whereBetween('created_at', [
            $this->fechaInicio . ' 00:00:00',
            $this->fechaFin . ' 23:59:59'
        ])->whereHas('user', function($q) {
            $q->whereDoesntHave('roles', function($roleQuery) {
                $roleQuery->where('name', 'Super Admin');
            });
        })->count();

        $nuevosUsuarios = UserActivity::where('tipo_actividad', 'registro')
            ->whereBetween('created_at', [
                $this->fechaInicio . ' 00:00:00',
                $this->fechaFin . ' 23:59:59'
            ])
            ->whereHas('user', function($q) {
                $q->whereDoesntHave('roles', function($roleQuery) {
                    $roleQuery->where('name', 'Super Admin');
                });
            })
            ->count();

        $ingresos = UserActivity::where('tipo_actividad', 'login')
            ->whereBetween('created_at', [
                $this->fechaInicio . ' 00:00:00',
                $this->fechaFin . ' 23:59:59'
            ])
            ->whereHas('user', function($q) {
                $q->whereDoesntHave('roles', function($roleQuery) {
                    $roleQuery->where('name', 'Super Admin');
                });
            })
            ->count();

        $salidas = UserActivity::where('tipo_actividad', 'logout')
            ->whereBetween('created_at', [
                $this->fechaInicio . ' 00:00:00',
                $this->fechaFin . ' 23:59:59'
            ])
            ->whereHas('user', function($q) {
                $q->whereDoesntHave('roles', function($roleQuery) {
                    $roleQuery->where('name', 'Super Admin');
                });
            })
            ->count();

        return view('livewire.reporte.ingresos', [
            'actividades' => $actividades,
            'totalRegistros' => $totalRegistros,
            'nuevosUsuarios' => $nuevosUsuarios,
            'ingresos' => $ingresos,
            'salidas' => $salidas,
        ]);
    }
}
