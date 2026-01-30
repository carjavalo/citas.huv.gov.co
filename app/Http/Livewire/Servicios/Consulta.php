<?php

namespace App\Http\Livewire\Servicios;

use App\Models\servicios;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Consulta extends Component
{
    use WithPagination;
    use AuthorizesRequests;
    
    public $modal = false;
    public $serv_id; //Se inializa el usuario hace click en el botón editar
    public $busqueda;
    public $estado = null;  //Filtros

    protected $listeners = ['cerrarModal', 'eliminarServicio'];

    public function eliminarServicio($id)
    {
        $servicio = servicios::find($id);

        if ($servicio) {
            $servicio->delete();
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
        return view('livewire.servicios.consulta', [
            'servicios' => servicios::where('servnomb','like','%'.$this->busqueda.'%')->orWhere('servcod','like','%'.$this->busqueda.'%')/* ->where('estado','like','$'.$this->estado) */->orderBy('servnomb','asc')->paginate(10),
        ]);
    }

    public function abrirModal($servicio_id)//Recibe el id del servicio mediante el boton editar
    {
        $this->serv_id = $servicio_id; //Se inicializa la variable con el valor del servicio
        $this->modal = true;
    }

    public function cerrarModal()
    {
        $this->modal = false;
    }
}
