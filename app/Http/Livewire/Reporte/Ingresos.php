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
    public $documento = ''; // Número de identificación del usuario

    public function mount()
    {
        $this->fechaInicio = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->fechaFin = Carbon::now()->format('Y-m-d');
    }

    public function aplicarFiltros()
    {
        $this->resetPage();
    }

    public function updatingDocumento()
    {
        $this->resetPage();
    }

    /**
     * Limpia el filtro de número de identificación.
     */
    public function limpiarDocumento()
    {
        $this->documento = '';
        $this->resetPage();
    }

    /**
     * Consulta base compartida por el listado y por las estadísticas, para que
     * los totales siempre correspondan a lo que se ve en la tabla.
     */
    private function baseQuery()
    {
        return UserActivity::whereBetween('created_at', [
                $this->fechaInicio . ' 00:00:00',
                $this->fechaFin . ' 23:59:59'
            ])
            ->whereHas('user', function($q) {
                // Excluir Super Admin
                $q->whereDoesntHave('roles', function($roleQuery) {
                    $roleQuery->where('name', 'Super Admin');
                });

                // Filtrar por número de identificación si se indicó
                if (trim($this->documento) !== '') {
                    $q->where('ndocumento', 'like', '%' . trim($this->documento) . '%');
                }
            });
    }

    public function exportarExcel()
    {
        $nombreArchivo = 'actividades_usuarios_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new ActividadesUsuariosExport($this->fechaInicio, $this->fechaFin, $this->tipoActividad, $this->documento),
            $nombreArchivo
        );
    }

    public function render()
    {
        // Query base para actividades
        $query = $this->baseQuery()->with('user');

        // Aplicar filtro de tipo si está seleccionado
        if ($this->tipoActividad && $this->tipoActividad !== '') {
            $query->where('tipo_actividad', $this->tipoActividad);
        }

        // Ordenar por fecha descendente y paginar
        $actividades = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estadísticas (respetan el filtro de documento igual que la tabla)
        $totalRegistros = $this->baseQuery()->count();

        $nuevosUsuarios = $this->baseQuery()->where('tipo_actividad', 'registro')->count();

        $ingresos = $this->baseQuery()->where('tipo_actividad', 'login')->count();

        $salidas = $this->baseQuery()->where('tipo_actividad', 'logout')->count();

        return view('livewire.reporte.ingresos', [
            'actividades' => $actividades,
            'totalRegistros' => $totalRegistros,
            'nuevosUsuarios' => $nuevosUsuarios,
            'ingresos' => $ingresos,
            'salidas' => $salidas,
        ]);
    }
}
