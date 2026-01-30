<?php

namespace App\Http\Livewire\Citas;

use Livewire\Component;
use App\Models\solicitudes;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Consulta extends Component
{
    use WithPagination;
    
    public function render()
    {
        $citas = solicitudes::where('pacid','=',Auth::user()->id)->
        join('users','solicitudes.pacid','=','users.id')->
        join('servicios','solicitudes.espec','=','servicios.servcod')->
        orderBy('id','asc')->
        get([
            'solicitudes.*',
            'users.email',
            'users.name',
            'users.apellido1',
            'users.apellido2',
            'servicios.servnomb'
        ]);
        
        
        return view('livewire.citas.consulta',[
            'citas'   => $citas,
        ]
        );
    }
}



