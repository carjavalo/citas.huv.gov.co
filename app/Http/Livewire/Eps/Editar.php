<?php

namespace App\Http\Livewire\Eps;

use App\Models\eps;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Editar extends Component
{
    use AuthorizesRequests;

    public $eps_id, $eps_nombre, $eps_estado;
    public $estados;
    
    public function mount($eps_id)
    {
        $this->authorize('admin.eps.edit');

        $datos = eps::where('id','=',$eps_id)->first();
        $this->eps_nombre = $datos->nombre;
        $this->eps_estado = $datos->estado;
        $this->eps_id = $datos->id;
        $this->estados = [
            'activo' => 'true',
            'inactivo'  => 'false',
        ];
    }

    protected $rules = [
        'eps_nombre'    => 'required',
        'eps_estado'    => 'required',
    ];

    public function updated($propiedad)
    {
        $this->validateOnly($propiedad);
    }

    public function render()
    {
        return view('livewire.eps.editar');
    }

    public function cerrarModal()
    {
        /* $this->reset(); */
        $this->emitTo('eps.consulta','cerrarModal');
    }

    public function editar()
    {
        $this->validate();

        eps::where('id','=',$this->eps_id)->update([
            'nombre'    => $this->eps_nombre,
            'estado'    => $this->eps_estado,
        ]);
        $this->cerrarModal();
        $this->emit('alertSuccess','Se editó la eps exitosamente.');  
    }

}
