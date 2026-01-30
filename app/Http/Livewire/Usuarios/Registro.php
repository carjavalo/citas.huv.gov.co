<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\eps;
use App\Models\tipo_identificacion;
use Livewire\Component;

class Registro extends Component
{
    public function render()
    {
        return view('livewire.usuarios.registro', [
            'aseguradoras' => eps::where('estado','=',true)->get(),
            'tipo_idenficacion' => tipo_identificacion::all(),
        ])->layout('layouts.guest');
    }
}
