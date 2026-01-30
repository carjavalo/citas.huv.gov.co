<?php

namespace App\Http\Livewire\Especialidades;

use App\Models\servicios;
use App\Models\Pservicio;
use App\Models\Sede;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Crear extends Component
{
    use AuthorizesRequests;

    public $especialidad_id;
    public $servnomb;
    public $servcod;
    public $sede_id;
    public $id_pservicios;
    public $estado = true;
    public $modoEdicion = false;

    protected $rules = [
        'servnomb' => 'required|min:2|max:255',
        'servcod' => 'required|min:1|max:50',
        'sede_id' => 'required|exists:sedes,id',
        'id_pservicios' => 'required|exists:pservicios,id',
        'estado' => 'required|boolean',
    ];

    protected $messages = [
        'servnomb.required' => 'El nombre de la especialidad es obligatorio',
        'servnomb.min' => 'El nombre debe tener al menos 2 caracteres',
        'servcod.required' => 'El código de la especialidad es obligatorio',
        'servcod.min' => 'El código debe tener al menos 1 caracter',
        'sede_id.required' => 'Debe seleccionar una sede',
        'sede_id.exists' => 'La sede seleccionada no existe',
        'id_pservicios.required' => 'Debe seleccionar un servicio',
        'id_pservicios.exists' => 'El servicio seleccionado no existe',
        'estado.required' => 'El estado es obligatorio',
    ];

    public function mount($especialidad_id = null)
    {
        $this->authorize('admin.servicios.consultar');
        
        if ($especialidad_id) {
            $this->modoEdicion = true;
            $this->especialidad_id = $especialidad_id;
            $especialidad = servicios::with('pservicio.sede')->findOrFail($especialidad_id);
            $this->servnomb = $especialidad->servnomb;
            $this->servcod = $especialidad->servcod;
            $this->id_pservicios = $especialidad->id_pservicios;
            $this->estado = $especialidad->estado;
            
            if ($especialidad->pservicio && $especialidad->pservicio->sede_id) {
                $this->sede_id = $especialidad->pservicio->sede_id;
            }
        }
    }

    public function updatedSedeId()
    {
        $this->id_pservicios = '';
    }

    public function render()
    {
        $sedes = Sede::where('estado', true)->orderBy('nombre', 'asc')->get();
        
        $pservicios = collect();
        if ($this->sede_id) {
            $pservicios = Pservicio::where('sede_id', $this->sede_id)->orderBy('descripcion', 'asc')->get();
        }
        
        return view('livewire.especialidades.crear', [
            'sedes' => $sedes,
            'pservicios' => $pservicios,
        ]);
    }

    public function guardar()
    {
        $this->validate();

        if ($this->modoEdicion) {
            $especialidad = servicios::findOrFail($this->especialidad_id);
            $especialidad->update([
                'servnomb' => $this->servnomb,
                'servcod' => $this->servcod,
                'id_pservicios' => $this->id_pservicios,
                'estado' => $this->estado,
            ]);
            $this->emit('alertSuccess', 'Especialidad actualizada exitosamente.');
            $this->emitTo('especialidades.consulta', 'especialidadActualizada');
        } else {
            servicios::create([
                'servnomb' => $this->servnomb,
                'servcod' => $this->servcod,
                'id_pservicios' => $this->id_pservicios,
                'estado' => $this->estado,
            ]);
            $this->emit('alertSuccess', 'Especialidad creada exitosamente.');
            $this->emitTo('especialidades.consulta', 'especialidadCreada');
        }
    }

    public function cerrar()
    {
        $this->emitTo('especialidades.consulta', 'cerrarModal');
    }
}
