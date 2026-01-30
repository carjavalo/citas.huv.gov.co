<?php

namespace App\Http\Livewire\Pservicios;

use App\Models\Pservicio;
use App\Models\Sede;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Consulta extends Component
{
    use WithPagination;
    use AuthorizesRequests;
    
    public $modal = false;
    public $modalCrear = false;
    public $pservicio_id;
    public $busqueda;
    public $filtroSede = '';

    protected $listeners = ['cerrarModal', 'pservicioCreado', 'pservicioActualizado', 'eliminarPservicio'];

    public function eliminarPservicio($id)
    {
        $pservicio = Pservicio::find($id);

        if ($pservicio) {
            $pservicio->delete();
            $this->emit('alertSuccess', 'Servicio eliminado correctamente');
        } else {
            $this->emit('alertError', 'No se encontró el servicio');
        }
    }

    public function mount()
    {
        $this->authorize('admin.servicios.consultar');
    }
    
    public function render()
    {
        $query = Pservicio::with('sede');

        if ($this->busqueda) {
            $query->where('descripcion', 'like', '%' . $this->busqueda . '%');
        }

        if ($this->filtroSede !== '') {
            $query->where('sede_id', $this->filtroSede);
        }

        $pservicios = $query->orderBy('descripcion', 'asc')->paginate(10);
        $sedes = Sede::where('estado', true)->orderBy('nombre', 'asc')->get();

        return view('livewire.pservicios.consulta', [
            'pservicios' => $pservicios,
            'sedes' => $sedes,
        ]);
    }

    public function abrirModalCrear()
    {
        $this->pservicio_id = null;
        $this->modalCrear = true;
    }

    public function abrirModalEditar($id)
    {
        $this->pservicio_id = $id;
        $this->modal = true;
    }

    public function cerrarModal()
    {
        $this->modal = false;
        $this->modalCrear = false;
    }

    public function pservicioCreado()
    {
        $this->cerrarModal();
    }

    public function pservicioActualizado()
    {
        $this->cerrarModal();
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroSede()
    {
        $this->resetPage();
    }
}
