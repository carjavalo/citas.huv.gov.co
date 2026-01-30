<?php

namespace App\Http\Livewire\Citas;

use App\Mail\RechazarCita as MailRechazarCita;
use App\Models\solicitudes;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class RechazarCita extends Component
{
    public $sol_id;
    public $datos, $paciente; //Variable que almacena los datos de la solicitud agendada
    public $mensaje;

    protected $rules = [
        'mensaje'   => 'required'
    ];

    protected $messages = [
        'mensaje.required' => 'Digite un mensaje para informar el motivo de rechazo al paciente.'
    ];

    public function mount($solicitud_id)
    {
        try {
            $this->sol_id = $solicitud_id;
            $this->datos = solicitudes::join('users', 'solicitudes.pacid','=','users.id')->
            join('eps','users.eps','=','eps.id')->
            join('servicios','solicitudes.espec','=','servicios.servcod')->
            where('solicitudes.id','=',$solicitud_id)->
            select(['solicitudes.*','users.name','users.email','users.apellido1','users.eps','eps.nombre','users.ndocumento','servicios.servnomb'])->first();
            $this->paciente = User::where('id','=',$this->datos->pacid)->select(['id','name','apellido1','apellido2','email','telefono1'])->first();
        } catch (\Throwable $th) {
            $this->emit('alertError',$th); //Evento para emitir alerta
        }
    }
    
    public function render()
    {
        return view('livewire.citas.rechazar-cita');
    }

    public function notificar()
    {
        $this->validate();
        
        $solicitud = solicitudes::where('id','=',$this->datos->id)->first();
        $solicitud->update([
            'estado'    => 'Rechazada',
            'motivo_rechazo'    => ''.$this->mensaje.'',
            'usercod'   => Auth::user()->id,
        ]);
        $usuario = $this->paciente->name.' '.$this->paciente->apellido1.' '.$this->paciente->apellido2;
        Mail::to($this->paciente->email)->send(new MailRechazarCita($this->mensaje,$usuario)); //SE ENVÍA CORREO CON LOS DATOS DE LA CITA
        $this->emitTo('citas.consulta-general','cerrarRechazar');
        $this->emit('alertSuccess','Notificación de rechazo al correo '.$this->paciente->email.' enviada satisfactoriamente.'); //Evento para emitir alerta

    }
}
