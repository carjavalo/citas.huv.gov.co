<?php

namespace App\Http\Livewire\Citas;

use App\Models\eps;
use App\Models\Pservicio;
use App\Models\servicios;
use App\Models\solicitudes;
use App\Models\Sede;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Solicitar extends Component
{
    public $codigo = false, $selectpespec = null, $selectespec = null, $servicios = null;
    public $selectsede = null, $pservicios = null;
    public $nombres, $apellido1, $apellido2, $pacid, $correo, $eps;
    public $espec,$pespec, $estado, $historia, $autorizacion, $ordenMedica, $pacdocid, $observacion, $num_soli, $codigo_autorizacion, $soporte_patologia;
    public $procesando = false; // Bandera para evitar doble envío

    use WithFileUploads;

    protected $messages = [
        'espec.required'                    => 'Seleccione una opción.',
        'autorizacion.required'             => 'Adjunte los archivos requeridos.',
        'ordenMedica.required'              => 'Adjunte los archivos requeridos.',
        'historia.required'                 => 'Adjunte los archivos requeridos.',
        'pacdocid.required'                 => 'Adjunte los archivos requeridos.',
        'autorizacion.mimes'                => 'Solo se admiten archivos .jpg .png .pdf', 
        'ordenMedica.mimes'                 => 'Solo se admiten archivos .jpg .png .pdf', 
        'historia.mimes'                    => 'Solo se admiten archivos .jpg .png .pdf', 
        'pacdocid.mimes'                    => 'Solo se admiten archivos .jpg .png .pdf', 
        'observacion.max'                   => 'Máximo 255 carácteres.',
        'ordenMedica.max'                   => 'El archivo no puede exceder 2 Megabytes.',
        'historia.max'                      => 'El archivo no puede exceder 2 Megabytes.',
        'pacdocid.max'                      => 'El archivo no puede exceder 2 Megabytes.',
        'soporte_patologia.required'        => 'Adjunte un soporte patológico.',
    ];

    public function mount()
    {
        $this->nombres      = Auth::user()->name;
        $this->apellido1    = Auth::user()->apellido1;
        $this->apellido2    = Auth::user()->apellido2;
        $this->pacid        = Auth::user()->id;
        $this->correo       = Auth::user()->email;
        $this->eps          = eps::where('id','=',Auth::user()->eps)->pluck('nombre');
        $this->servicios    = null;
    }

    public function render()
    {
        return view('livewire.citas.solicitar',[
            'sedes' => Sede::where('estado', true)->get(),
        ]);
    }

    public function updatedselectsede()
    {
        $this->pservicios = null;
        $this->selectpespec = null;
        $this->servicios = null;
        $this->espec = null;
        
        if ($this->selectsede) {
            $this->pservicios = Pservicio::where('sede_id', $this->selectsede)
                ->where('activo', true)
                ->get();
        }
    }

    public function updatedselectpespec()
    {
        $this->servicios = null;
        $this->espec = null;
        
        if ($this->selectpespec) {
            $this->servicios = servicios::where('id_pservicios', $this->selectpespec)
                ->where('estado', '=', true)
                ->orderBy('servnomb', 'asc')
                ->get();
        }
    }
    
    public function agendar()
    {
        // Evitar doble envío: si ya está procesando, no ejecutar de nuevo
        if ($this->procesando) {
            return;
        }
        $this->procesando = true;

        // SEGURIDAD: Siempre usar Auth::user()->id, nunca confiar en $this->pacid del frontend
        $userId = Auth::user()->id;

        // Validación extra: evitar duplicados por usuario, especialidad y estado pendiente en el mismo día
        $existe = solicitudes::where('pacid', $userId)
            ->where('espec', $this->espec)
            ->where('estado', 'Pendiente')
            ->whereDate('created_at', now()->toDateString())
            ->exists();
        if ($existe) {
            $this->emit('alertError', 'Ya existe una solicitud pendiente para esta especialidad hoy.');
            $this->procesando = false;
            return;
        }

        if($this->espec == 1 || $this->espec == 491 || $this->espec == 4){

            $this->validate([
                'nombres'       => 'required',
                'pacid'         => 'required',
                'apellido1'     => 'required',
                'correo'        => 'required|email',
                'espec'         => 'required',
                /* 'autorizacion'  => 'mimes:pdf,jpg,png', */
                'ordenMedica'   => 'required|mimes:pdf,jpg,png|max:1900',
                'historia'      => 'required|mimes:pdf,jpg,png|max:1900',
                'pacdocid'      => 'required|mimes:pdf,jpg,png|max:1900',
                'observacion'   => 'max:255',
                'soporte_patologia'       => 'required|mimes:pdf,jpg,png|max:1900',
            ]); 
        }else{
            $this->validate([
                'nombres'       => 'required',
                'pacid'         => 'required',
                'apellido1'     => 'required',
                'correo'        => 'required|email',
                'espec'         => 'required',
                /* 'autorizacion'  => 'mimes:pdf,jpg,png', */
                'ordenMedica'   => 'required|mimes:pdf,jpg,png|max:1900',
                'historia'      => 'required|mimes:pdf,jpg,png|max:2000',
                'pacdocid'      => 'required|mimes:pdf,jpg,png|max:2000',
                'observacion'   => 'max:255',
            ]); 
        }
        
        try {
            // 1. Capturar nombres originales de archivos ANTES de cualquier operación
            $nombreHistoria = $this->historia->getClientOriginalName();
            $nombreOrdenMedica = $this->ordenMedica->getClientOriginalName();
            $nombreDocId = $this->pacdocid->getClientOriginalName();
            $nombreAutorizacion = $this->autorizacion ? $this->autorizacion->getClientOriginalName() : null;
            $nombreSoportePatologia = $this->soporte_patologia ? $this->soporte_patologia->getClientOriginalName() : null;

            // 2. Crear registro en BD con solnum atómico (TODO dentro de la misma transacción)
            $sol = DB::transaction(function () use ($userId, $nombreHistoria, $nombreOrdenMedica, $nombreDocId, $nombreAutorizacion, $nombreSoportePatologia) {
                // Obtener el siguiente solnum con bloqueo para evitar duplicados
                $ultimoSolnum = solicitudes::where('pacid', $userId)
                    ->lockForUpdate()
                    ->max('solnum');
                $numero = ($ultimoSolnum ?? 0) + 1;

                // Definir la ruta base de archivos usando SIEMPRE el ID del usuario autenticado
                $rutaBase = 'Documentos/usuario' . $userId . '/solicitud_' . $numero;

                // Crear el registro en la base de datos DENTRO de la transacción
                return solicitudes::create([
                    'pacid'                 => $userId,
                    'espec'                 => $this->espec,
                    'estado'                => 'Pendiente',
                    'solnum'                => $numero,
                    'pachis'                => $rutaBase . '/' . $nombreHistoria,
                    'pacordmed'             => $rutaBase . '/' . $nombreOrdenMedica,
                    'pacauto'               => $nombreAutorizacion ? $rutaBase . '/' . $nombreAutorizacion : null,
                    'codigo_autorizacion'   => $this->codigo_autorizacion,
                    'pacdocid'              => $rutaBase . '/' . $nombreDocId,
                    'pacobs'                => $this->observacion,
                    'soporte_patologia'     => $nombreSoportePatologia ? $rutaBase . '/' . $nombreSoportePatologia : null,
                ]);
            });

            // 3. Ahora que el registro está creado, almacenar archivos usando la ruta del registro
            $rutaBase = 'Documentos/usuario' . $userId . '/solicitud_' . $sol->solnum;

            // 4. Almacenar archivos obligatorios
            $this->historia->storeAs($rutaBase, $nombreHistoria, 'upload');
            $this->ordenMedica->storeAs($rutaBase, $nombreOrdenMedica, 'upload');
            $this->pacdocid->storeAs($rutaBase, $nombreDocId, 'upload');

            // 5. Almacenar archivos opcionales
            if ($this->autorizacion) {
                $this->autorizacion->storeAs($rutaBase, $nombreAutorizacion, 'upload');
            }
            if ($this->soporte_patologia) {
                $this->soporte_patologia->storeAs($rutaBase, $nombreSoportePatologia, 'upload');
            }

            // 6. Verificar que el ID fue asignado correctamente
            if (empty($sol->id)) {
                \Log::error('Solicitud creada sin id asignado', [
                    'pacid' => $userId,
                    'solnum' => $sol->solnum,
                    'model' => $sol->toArray(),
                ]);
                $sol->refresh();
            }

            \Log::info('Solicitud creada exitosamente', [
                'pacid' => $userId,
                'solnum' => $sol->solnum,
                'id' => $sol->id,
            ]);

            $this->emit('alertSuccessCita', 'Solicitud enviada satisfactoriamente. ID: ' . $sol->id);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Re-lanzar excepciones de validación para que Livewire las maneje
        } catch (\Throwable $th) {
            \Log::error('Error al crear solicitud', [
                'pacid' => $userId,
                'error' => $th->getMessage(),
                'archivo' => $th->getFile(),
                'linea' => $th->getLine(),
            ]);
            $this->emit('alertError', 'Ocurrió un error al crear la solicitud. Por favor intente nuevamente.');
        } finally {
            $this->procesando = false; // Liberar la bandera siempre
        }
    }
}