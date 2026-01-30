<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class Tablero extends Component
{
    public function render()
    {
        return view('livewire.tablero', [
            'pacientes'     => User::where('tdocumento','=','2')->get(),
        ]);
    }
}
