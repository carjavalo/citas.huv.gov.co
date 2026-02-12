<?php

namespace App\Http\Livewire\Citas;

use App\Models\solicitudes;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificarEspera as MailNotificarEspera;
use App\Models\servicios;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificarEspera extends Component
{
    public $sol_id;
    public $datos, $paciente; //Variable que almacena los datos de la solicitud agendada
    public $mensaje, $especialidades, $espec;
    public $editar = false;

    protected $rules = [
        'mensaje'   => 'required'
    ];

    protected $messages = [
        'mensaje.required' => 'Digite un mensaje para informar el motivo de rechazo al paciente.'
    ];

    public function mount($solicitud_id = null)
    {
        if (empty($solicitud_id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud.');
            return;
        }
        try { 
            $this->sol_id = $solicitud_id;
            $this->datos = solicitudes::join('users', 'solicitudes.pacid','=','users.id')->
            join('eps','users.eps','=','eps.id')->
            join('servicios','solicitudes.espec','=','servicios.servcod')->
            where('solicitudes.id','=',$solicitud_id)->
            select(['solicitudes.*','users.name','users.email','users.apellido1','users.eps','eps.nombre','users.ndocumento','servicios.servnomb'])->first();
            $this->paciente = User::where('id','=',$this->datos->pacid)->select(['id','name','apellido1','apellido2','email','telefono1'])->first();
            $this->mensaje = 'Informamos que en el momento no contamos con agenda para la especialidad que requiere, por tal motivo usted pasará a lista de espera. Por favor estar pendiente de una respuesta por este medio, o comunicarse con su EPS para el cambio de prestador'; //Solución temporal mientras se implementa la tabla RespuestaSolicitud
        } catch (\Throwable $th) {
            $this->emit('alertError',$th); //Evento para emitir alerta
        }
    }
    
    public function render()
    {
        return view('livewire.citas.notificar-espera');
    }

    public function notificar()
    {
        $this->validate();

        $solicitud = solicitudes::where('id','=',$this->datos->id)->first();
        if($this->editar && isset($this->espec)){
            $solicitud->update([
                'espec'   =>  $this->espec     
            ]);
        } 
        $solicitud->update([
            'estado'    => 'Espera',
            'motivo_espera'    => ''.$this->mensaje.'',
            'usercod'   => Auth::user()->id,
        ]);
        $usuario = $this->paciente->name.' '.$this->paciente->apellido1.' '.$this->paciente->apellido2;
        $fecha_solicitud = $solicitud->created_at->format('Y/m/d H:i:s');
        try {
            Mail::to($this->paciente->email)->send(new MailNotificarEspera($this->mensaje,$usuario,$fecha_solicitud)); //SE ENVÍA CORREO CON LOS DATOS DE LA CITA
            $this->emitTo('citas.consulta-general','cerrarNotificarEspera');
            $this->emit('alertSuccess','Notificación de espera al correo '.$this->paciente->email.' enviada satisfactoriamente.'); //Evento para emitir alerta    
        } catch (\Throwable $th) {
            $this->emit('alertError','Ocurrió un error'.$th); //Evento para emitir alerta de error
        }

    }
    public function edit($sol_id)
    {
        $this->sol_id = $sol_id;
        $this->especialidades = servicios ::where('estado','=',true)->get();
        $this->editar = true;
    }


}


