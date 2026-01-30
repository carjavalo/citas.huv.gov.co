<?php

namespace App\Http\Livewire\Eps;

use Livewire\Component;
use App\Models\eps;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Consulta extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public $modal = false;
    public $id_eps;
    public $busqueda;

    protected $listeners = ['cerrarModal'];

    public function mount()
    {
        $this->authorize('admin.eps.consultar');
    }
    public function render()
    {
        return view('livewire.eps.consulta',[
            'aseguradoras' => eps::where('nombre','like','%'. $this->busqueda .'%')->orderBy('nombre','asc')->paginate(20),
        ]);
    }

    public function abrirModal($id)
    {
        $this->id_eps= $id;
        $this->modal = true;
    }

    public function cerrarModal()
    {
        $this->modal = false;
    }
}
