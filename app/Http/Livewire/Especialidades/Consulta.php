<?php

namespace App\Http\Livewire\Especialidades;

use App\Models\servicios;
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
    public $especialidad_id;
    public $busqueda;
    public $filtroSede = '';
    public $filtroServicio = '';
    public $filtroEstado = '';

    protected $listeners = ['cerrarModal', 'especialidadCreada', 'especialidadActualizada'];

    public function mount()
    {
        $this->authorize('admin.servicios.consultar');
    }

    public function updatedFiltroSede()
    {
        $this->filtroServicio = '';
        $this->resetPage();
    }
    
    public function render()
    {
        $query = servicios::with('pservicio.sede');

        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('servnomb', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('servcod', 'like', '%' . $this->busqueda . '%');
            });
        }

        if ($this->filtroSede !== '') {
            $query->whereHas('pservicio', function($q) {
                $q->where('sede_id', $this->filtroSede);
            });
        }

        if ($this->filtroServicio !== '') {
            $query->where('id_pservicios', $this->filtroServicio);
        }

        if ($this->filtroEstado !== '') {
            $query->where('estado', $this->filtroEstado);
        }

        $especialidades = $query->orderBy('servnomb', 'asc')->paginate(10);
        
        $sedes = Sede::where('estado', true)->orderBy('nombre', 'asc')->get();
        
        $pservicios = collect();
        if ($this->filtroSede !== '') {
            $pservicios = Pservicio::where('sede_id', $this->filtroSede)->orderBy('descripcion', 'asc')->get();
        } else {
            $pservicios = Pservicio::with('sede')->orderBy('descripcion', 'asc')->get();
        }

        return view('livewire.especialidades.consulta', [
            'especialidades' => $especialidades,
            'sedes' => $sedes,
            'pservicios' => $pservicios,
        ]);
    }

    public function abrirModalCrear()
    {
        $this->especialidad_id = null;
        $this->modalCrear = true;
    }

    public function abrirModalEditar($id)
    {
        $this->especialidad_id = $id;
        $this->modal = true;
    }

    public function cerrarModal()
    {
        $this->modal = false;
        $this->modalCrear = false;
    }

    public function especialidadCreada()
    {
        $this->cerrarModal();
    }

    public function especialidadActualizada()
    {
        $this->cerrarModal();
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroServicio()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }
}
