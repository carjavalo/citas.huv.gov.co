<?php

namespace App\Http\Livewire\Citas;

use App\Models\solicitudes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetalleCita extends Component
{
    public $sol_id;
    public $datos, $agente; //Variable que almacena los datos de la solicitud agendada

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
            select([
                'solicitudes.*',
                DB::raw('users.name as paciente'),
                'users.email',
                'users.apellido1', 
                DB::raw('users.ndocumento as numero_documento'), 
                DB::raw('eps.nombre as eps') ,
                'servicios.servnomb'
            ])->first();
           
            $this->agente = User::select([
                'id',
                'name',
                'apellido1',
                'apellido2',
                'email',
                'ndocumento'
            ])->where('id','=',$this->datos->usercod)->first();
        } catch (\Throwable $th) {
            $this->emit('alertError',$th); //Evento para emitir alerta
        }
    }
    public function render()
    {
        return view('livewire.citas.detalle-cita');
    }
}
