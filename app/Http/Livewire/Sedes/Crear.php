<?php

namespace App\Http\Livewire\Sedes;

use App\Models\Sede;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Crear extends Component
{
    use AuthorizesRequests;

    public $sede_id;
    public $nombre;
    public $ciudad;
    public $estado = true;
    public $modoEdicion = false;

    protected $rules = [
        'nombre' => 'required|min:2|max:255',
        'ciudad' => 'required|min:2|max:255',
        'estado' => 'required|boolean',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre de la sede es obligatorio',
        'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
        'ciudad.required' => 'La ciudad es obligatoria',
        'ciudad.min' => 'La ciudad debe tener al menos 2 caracteres',
        'estado.required' => 'El estado es obligatorio',
    ];

    public function mount($sede_id = null)
    {
        $this->authorize('admin.servicios.consultar');
        
        if ($sede_id) {
            $this->modoEdicion = true;
            $this->sede_id = $sede_id;
            $sede = Sede::findOrFail($sede_id);
            $this->nombre = $sede->nombre;
            $this->ciudad = $sede->ciudad;
            $this->estado = $sede->estado;
        }
    }

    public function render()
    {
        return view('livewire.sedes.crear');
    }

    public function guardar()
    {
        $this->validate();

        if ($this->modoEdicion) {
            $sede = Sede::findOrFail($this->sede_id);
            $sede->update([
                'nombre' => $this->nombre,
                'ciudad' => $this->ciudad,
                'estado' => $this->estado,
            ]);
            $this->emit('alertSuccess', 'Sede actualizada exitosamente.');
            $this->emitTo('sedes.consulta', 'sedeActualizada');
        } else {
            Sede::create([
                'nombre' => $this->nombre,
                'ciudad' => $this->ciudad,
                'estado' => $this->estado,
            ]);
            $this->emit('alertSuccess', 'Sede creada exitosamente.');
            $this->emitTo('sedes.consulta', 'sedeCreada');
        }
    }

    public function cerrar()
    {
        $this->emitTo('sedes.consulta', 'cerrarModal');
    }
}
