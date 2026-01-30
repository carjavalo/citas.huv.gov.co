<?php

namespace App\Http\Livewire\Servicios;

use App\Models\servicios;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Editar extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public $servicio, $serv_id;
    public $nombre, $estado;

    protected $rules = [
        'nombre'    => 'required',
        'estado'    => 'required',
    ];

    protected $messages = [
        'nombre.required'   => 'El nombre no puede quedar vacío',
        'estado.required'   => 'Elegir un estado es obligatorio'
    ];

    public function mount($serv_id)
    {
        $this->authorize('admin.servicios.edit');
        $this->servicio = servicios::where('id','=',$serv_id)->first();  //Recibe el id del servicio
        $this->nombre = $this->servicio->servnomb;
        $this->estado = $this->servicio->estado;
    }

    public function render()
    {
        return view('livewire.servicios.editar');
    }
    
    public function editar()
    {

        $this->validate();

        $this->servicio->update([
            'servnomb'  => $this->nombre,
            'estado'    => $this->estado,
        ]);

        $this->emitTo('servicios.consulta','cerrarModal');
        $this->emit('alertSuccess','Se editó el servicio '.$this->nombre.' exitosamente.');  
    }
}
