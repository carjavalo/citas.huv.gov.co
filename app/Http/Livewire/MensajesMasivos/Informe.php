<?php

namespace App\Http\Livewire\MensajesMasivos;

use App\Exports\CitacionesMasivasExport;
use App\Models\CitacionMasiva;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

/** Informe de las citaciones enviadas y de lo que respondió cada paciente. */
class Informe extends Component
{
    use WithPagination;

    public $busqueda = '';
    public $estado = '';
    public $lote = '';
    public $desde;
    public $hasta;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'estado'   => ['except' => ''],
        'lote'     => ['except' => ''],
        'desde'    => ['except' => null],
        'hasta'    => ['except' => null],
    ];

    public function mount()
    {
        $this->autorizar();
    }

    /** El middleware 'role' de la ruta no se vuelve a aplicar en las peticiones de Livewire. */
    public function hydrate()
    {
        $this->autorizar();
    }

    public function updating($propiedad)
    {
        if (in_array($propiedad, ['busqueda', 'estado', 'lote', 'desde', 'hasta'])) {
            $this->resetPage();
        }
    }

    public function filtrarEstado($estado)
    {
        $this->estado = isset(CitacionMasiva::ESTADOS[$estado]) ? $estado : '';
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['busqueda', 'estado', 'lote', 'desde', 'hasta']);
        $this->resetPage();
    }

    public function exportar()
    {
        return Excel::download(
            new CitacionesMasivasExport($this->filtros()),
            'confirmaciones_citas_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function render()
    {
        // Los totales respetan todos los filtros menos el de estado, para comparar respuestas.
        $totales = CitacionMasiva::query()
            ->filtrar(array_merge($this->filtros(), ['estado' => '', 'incluirFallidos' => true]))
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $lotes = CitacionMasiva::query()
            ->selectRaw("lote, archivo, min(enviado_at) as enviado, sum(case when estado <> 'fallido' then 1 else 0 end) as total")
            ->groupBy('lote', 'archivo')
            ->orderByDesc('enviado')
            ->limit(50)
            ->get();

        return view('livewire.mensajes-masivos.informe', [
            'citaciones' => CitacionMasiva::query()
                ->filtrar($this->filtros())
                ->orderByDesc('enviado_at')
                ->orderBy('paciente')
                ->orderBy('id')
                ->paginate(50),
            'totalEnviadas'  => $totales->sum() - ($totales[CitacionMasiva::FALLIDO] ?? 0),
            'totalAsistira'  => $totales[CitacionMasiva::ASISTIRA] ?? 0,
            'totalNoAsistira' => $totales[CitacionMasiva::NO_ASISTIRA] ?? 0,
            'totalPendiente' => $totales[CitacionMasiva::PENDIENTE] ?? 0,
            'totalFallido'   => $totales[CitacionMasiva::FALLIDO] ?? 0,
            'lotes'          => $lotes,
            'sedes'          => Consulta::SEDES,
        ]);
    }

    private function filtros()
    {
        return [
            'busqueda' => $this->busqueda,
            'estado'   => $this->estado,
            'lote'     => $this->lote,
            'desde'    => $this->validarFecha($this->desde),
            'hasta'    => $this->validarFecha($this->hasta),
        ];
    }

    private function validarFecha($fecha)
    {
        return is_string($fecha) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) ? $fecha : null;
    }

    private function autorizar(): void
    {
        abort_unless(Auth::check() && Auth::user()->can('admin.mensajes_masivos.consultar'), 403);
    }
}
