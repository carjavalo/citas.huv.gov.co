<?php

namespace App\Http\Livewire\Citas;

use App\Models\solicitudes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Agendar extends Component
{
    public $nombres, $apellido1, $apellido2, $pacid, $correo, $espec, $estado, $historia, $autorizacion, $ordenMedica;

    use WithFileUploads;

    public function mount()
    {
        $this->nombres = Auth::user()->name;
        $this->apellido1 = Auth::user()->apellido1;
        $this->apellido2 = Auth::user()->apellido2;
        $this->pacid = Auth::user()->id;
        $this->correo = Auth::user()->email;
    }

    public function render()
    {
        return view('livewire.citas.agendar');
    }

    public function agendar()
    {

            $this->validate([
                'nombres'       => 'required',
                'pacid'         => 'required',
                'apellido1'     => 'required',
                'apellido2'     => 'required',
                'correo'        => 'required|email',
                'espec'         => 'required',
                'autorizacion'  => 'required',
                'ordenMedica'   => 'required',
                'historia'      => 'required',
            ]);
                // Obtener el siguiente número de solicitud basado en el último solnum del usuario en la BD
                $ultimoSolnum = solicitudes::where('pacid', Auth::user()->id)->max('solnum');
                $numero = ($ultimoSolnum ?? 0) + 1;
                $extension = $this->historia->getClientOriginalExtension();
                $this->historia->storeAs('Documentos/usuario'.Auth::user()->id.'/solicitud_'.$numero,'historia.'.$extension, 'upload');
                $this->autorizacion->storeAs('Documentos/usuario'.Auth::user()->id.'/solicitud_'.$numero,'autorizacion.'.$extension, 'upload');    
                $this->ordenMedica->storeAs('Documentos/usuario'.Auth::user()->id.'/solicitud_'.$numero,'orden_medica.'.$extension, 'upload');
                $sol = solicitudes::create([
                    'pacid'     => $this->pacid,
                    'espec'     => $this->espec,
                    'estado'    => 'Pendiente',
                    'pachis'    => 'Documentos/usuario'.Auth::user()->id.''.'/solicitud_'.$numero.'/historia.'.$extension,
                    'pacordmed' => 'Documentos/usuario'.Auth::user()->id.''.'/solicitud_'.$numero.'/orden_medica.'.$extension,
                    'pacauto'   => 'Documentos/usuario'.Auth::user()->id.''.'/solicitud_'.$numero.'/autorizacion.'.$extension,
                ]);

                \Log::info('Solicitud creada (agendar)', ['pacid' => $this->pacid, 'solnum' => $numero, 'id' => $sol->id ?? null]);
                if (empty($sol->id)) {
                    \Log::error('Solicitud creada sin id asignado (agendar)', ['pacid' => $this->pacid, 'solnum' => $numero]);
                    $this->emit('alertError','Solicitud creada pero el identificador no fue asignado. Informe al administrador.');
                } else {
                    $this->reset(['espec','autorizacion','ordenMedica','historia']);
                    $this->emit('alertSuccess','Solicitud enviada satisfactoriamente'); //Evento para emitir alerta
                }
    }
}
