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
        $this->estado = filter_var($this->servicio->estado, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
    }

    public function render()
    {
        return view('livewire.servicios.editar');
    }
    
    public function editar()
    {

        $this->validate();

        $estado = filter_var($this->estado, FILTER_VALIDATE_BOOLEAN);

        $this->servicio->update([
            'servnomb'  => $this->nombre,
            'estado'    => $estado,
        ]);

        $this->emitTo('servicios.consulta','cerrarModal');
        $this->emit('alertSuccess','Se editó el servicio '.$this->nombre.' exitosamente.');  
    }
}
