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

            // SEGURIDAD: Siempre usar Auth::user()->id, nunca confiar en $this->pacid del frontend
            $userId = Auth::user()->id;

            try {
                $extension = $this->historia->getClientOriginalExtension();

                // Crear registro en BD con solnum atómico (todo dentro de la misma transacción)
                $sol = \Illuminate\Support\Facades\DB::transaction(function () use ($userId, $extension) {
                    $ultimoSolnum = solicitudes::where('pacid', $userId)
                        ->lockForUpdate()
                        ->max('solnum');
                    $numero = ($ultimoSolnum ?? 0) + 1;

                    $rutaBase = 'Documentos/usuario' . $userId . '/solicitud_' . $numero;

                    return solicitudes::create([
                        'pacid'     => $userId,
                        'espec'     => $this->espec,
                        'estado'    => 'Pendiente',
                        'solnum'    => $numero,
                        'pachis'    => $rutaBase . '/historia.' . $extension,
                        'pacordmed' => $rutaBase . '/orden_medica.' . $extension,
                        'pacauto'   => $rutaBase . '/autorizacion.' . $extension,
                    ]);
                });

                // Almacenar archivos DESPUÉS de que el registro fue creado exitosamente
                $rutaBase = 'Documentos/usuario' . $userId . '/solicitud_' . $sol->solnum;
                $this->historia->storeAs($rutaBase, 'historia.' . $extension, 'upload');
                $this->autorizacion->storeAs($rutaBase, 'autorizacion.' . $extension, 'upload');    
                $this->ordenMedica->storeAs($rutaBase, 'orden_medica.' . $extension, 'upload');

                \Log::info('Solicitud creada (agendar)', ['pacid' => $userId, 'solnum' => $sol->solnum, 'id' => $sol->id ?? null]);
                if (empty($sol->id)) {
                    \Log::error('Solicitud creada sin id asignado (agendar)', ['pacid' => $userId, 'solnum' => $sol->solnum]);
                    $this->emit('alertError','Solicitud creada pero el identificador no fue asignado. Informe al administrador.');
                } else {
                    $this->reset(['espec','autorizacion','ordenMedica','historia']);
                    $this->emit('alertSuccess','Solicitud enviada satisfactoriamente'); //Evento para emitir alerta
                }
            } catch (\Throwable $th) {
                \Log::error('Error al crear solicitud (agendar)', [
                    'pacid' => $userId,
                    'error' => $th->getMessage(),
                ]);
                $this->emit('alertError', 'Ocurrió un error al crear la solicitud. Por favor intente nuevamente.');
            }
    }
}
