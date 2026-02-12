<?php

namespace App\Http\Livewire\Citas;

use App\Models\eps;
use App\Models\Pservicio;
use App\Models\servicios;
use App\Models\solicitudes;
use App\Models\Sede;
use Illuminate\Support\Facades\Auth;
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
            // Obtener el siguiente número de solicitud basado en el último solnum del usuario en la BD
            $ultimoSolnum = solicitudes::where('pacid', Auth::user()->id)->max('solnum');
            $numero = ($ultimoSolnum ?? 0) + 1;
            $this->historia->storeAs('Documentos/usuario'.Auth::user()->id.'/solicitud_'.$numero,$this->historia->getClientOriginalName(), 'upload');
            $this->ordenMedica->storeAs('Documentos/usuario'.Auth::user()->id.'/solicitud_'.$numero,$this->ordenMedica->getClientOriginalName(), 'upload');
            $this->pacdocid->storeAs('Documentos/usuario'.Auth::user()->id.'/solicitud_'.$numero,$this->pacdocid->getClientOriginalName(), 'upload');
            if(!$this->autorizacion){//Si el paciente no adjuntó autorización la variable se inicializa con null
                $this->autorizacion = null;
            }else{
                $this->autorizacion->storeAs('Documentos/usuario'.Auth::user()->id.'/solicitud_'.$numero,$this->autorizacion->getClientOriginalName(), 'upload');    
            }
            if(!$this->soporte_patologia){//Si el paciente no adjuntó soporte la variable se inicializa con null
                $this->soporte_patologia = null;
            }else{
                $this->soporte_patologia->storeAs('Documentos/usuario'.Auth::user()->id.'/solicitud_'.$numero,$this->soporte_patologia->getClientOriginalName(), 'upload');    
            }
                $sol = solicitudes::create([
                    'pacid'                 => $this->pacid,
                    'espec'                 => $this->espec,
                    'estado'                => 'Pendiente',
                    'solnum'                => $numero,
                    'pachis'                => 'Documentos/usuario'.Auth::user()->id.''.'/solicitud_'.$numero.'/'.$this->historia->getClientOriginalName(),
                    'pacordmed'             => 'Documentos/usuario'.Auth::user()->id.''.'/solicitud_'.$numero.'/'.$this->ordenMedica->getClientOriginalName(),
                    'pacauto'               => $this->autorizacion== null ? null:'Documentos/usuario'.Auth::user()->id.''.'/solicitud_'.$numero.'/'.$this->autorizacion->getClientOriginalName(),// Si la variable es null, se inserta valor null, sino, se inserta la ruta del archivo
                    'codigo_autorizacion'   => $this->codigo_autorizacion,
                    'pacdocid'              => 'Documentos/usuario'.Auth::user()->id.''.'/solicitud_'.$numero.'/'.$this->pacdocid->getClientOriginalName(),
                    'pacobs'                => $this->observacion,
                    'soporte_patologia'     => $this->soporte_patologia== null ? null:'Documentos/usuario'.Auth::user()->id.''.'/solicitud_'.$numero.'/'.$this->soporte_patologia->getClientOriginalName(),
                ]);
                // Registrar en logs el id devuelto por la creación
                \Log::info('Solicitud creada', ['pacid' => $this->pacid, 'solnum' => $numero, 'id' => $sol->id ?? null]);
                if (empty($sol->id)) {
                    \Log::error('Solicitud creada sin id asignado', ['pacid' => $this->pacid, 'solnum' => $numero]);
                    $this->emit('alertError','Solicitud creada pero el identificador no fue asignado. Informe al administrador.');
                } else {
                    $this->emit('alertSuccessCita','Solicitud enviada satisfactoriamente'); //Evento para emitir alerta
                }
        } catch (\Throwable $th) {
            $this->emit('alertError','Ocurrió un error'.$th); //Evento para emitir alerta
        } finally {
            $this->procesando = false; // Liberar la bandera siempre
        }
    }
}