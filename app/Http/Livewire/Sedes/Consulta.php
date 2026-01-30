<?php

namespace App\Http\Livewire\Sedes;

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
    public $sede_id;
    public $busqueda;
    public $filtroEstado = '';

    protected $listeners = ['cerrarModal', 'sedeCreada', 'sedeActualizada', 'eliminarSede'];

    public function eliminarSede($id)
    {
        $sede = Sede::find($id);

        if ($sede) {
            $sede->delete();
            $this->emit('alertSuccess', 'Sede eliminada correctamente');
        } else {
            $this->emit('alertError', 'No se encontró la sede');
        }
    }

    public function mount()
    {
        $this->authorize('admin.servicios.consultar');
    }
    
    public function render()
    {
        $query = Sede::query();

        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('ciudad', 'like', '%' . $this->busqueda . '%');
            });
        }

        if ($this->filtroEstado !== '') {
            $query->where('estado', $this->filtroEstado);
        }

        $sedes = $query->orderBy('nombre', 'asc')->paginate(10);

        return view('livewire.sedes.consulta', [
            'sedes' => $sedes,
        ]);
    }

    public function abrirModalCrear()
    {
        $this->sede_id = null;
        $this->modalCrear = true;
    }

    public function abrirModalEditar($id)
    {
        $this->sede_id = $id;
        $this->modal = true;
    }

    public function cerrarModal()
    {
        $this->modal = false;
        $this->modalCrear = false;
    }

    public function sedeCreada()
    {
        $this->cerrarModal();
    }

    public function sedeActualizada()
    {
        $this->cerrarModal();
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }
}
