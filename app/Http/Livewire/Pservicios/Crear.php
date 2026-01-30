<?php

namespace App\Http\Livewire\Pservicios;

use App\Models\Pservicio;
use App\Models\Sede;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Crear extends Component
{
    use AuthorizesRequests;

    public $pservicio_id;
    public $descripcion;
    public $sede_id;
    public $activo = true;
    public $modoEdicion = false;

    protected $rules = [
        'descripcion' => 'required|min:2|max:255',
        'sede_id' => 'required|exists:sedes,id',
    ];

    protected $messages = [
        'descripcion.required' => 'La descripción del servicio es obligatoria',
        'descripcion.min' => 'La descripción debe tener al menos 2 caracteres',
        'sede_id.required' => 'Debe seleccionar una sede',
        'sede_id.exists' => 'La sede seleccionada no existe',
    ];

    public function mount($pservicio_id = null)
    {
        $this->authorize('admin.servicios.consultar');
        
        if ($pservicio_id) {
            $this->modoEdicion = true;
            $this->pservicio_id = $pservicio_id;
            $pservicio = Pservicio::findOrFail($pservicio_id);
            $this->descripcion = $pservicio->descripcion;
            $this->sede_id = $pservicio->sede_id;
            $this->activo = $pservicio->activo ?? true;
        }
    }

    public function render()
    {
        $sedes = Sede::where('estado', true)->orderBy('nombre', 'asc')->get();
        
        return view('livewire.pservicios.crear', [
            'sedes' => $sedes,
        ]);
    }

    public function guardar()
    {
        $this->validate();

        if ($this->modoEdicion) {
            $pservicio = Pservicio::findOrFail($this->pservicio_id);
            $pservicio->update([
                'descripcion' => $this->descripcion,
                'sede_id' => $this->sede_id,
                'activo' => $this->activo,
            ]);
            $this->emit('alertSuccess', 'Servicio actualizado exitosamente.');
            $this->emitTo('pservicios.consulta', 'pservicioActualizado');
        } else {
            Pservicio::create([
                'descripcion' => $this->descripcion,
                'sede_id' => $this->sede_id,
                'activo' => $this->activo,
            ]);
            $this->emit('alertSuccess', 'Servicio creado exitosamente.');
            $this->emitTo('pservicios.consulta', 'pservicioCreado');
        }
    }

    public function cerrar()
    {
        $this->emitTo('pservicios.consulta', 'cerrarModal');
    }
}
