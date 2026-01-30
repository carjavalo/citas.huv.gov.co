<?php

namespace App\Http\Livewire;

use App\Mail\Obstetrico\{
    SolvedRequest,
    OnRevisionRequest,
    RejectedRequest
};
use Livewire\{
    Component,
    WithFileUploads
};
use App\Models\{
    eps,
    PacienteObstetrica,
    servicios,
    SolicitudObstetricia,
    tipo_identificacion,
    EstadoSolicitud,
    Hospital,
};
use Illuminate\Support\{
    Str,
    Facades\Auth,
    Facades\Mail
};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class GodsonRequestComponent extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    /**
     * Component parameters
     */
    public $tiposIdentificacion, $createRequest, $eps, $especialidades, $isHospital, $user, $hospitals, $modalAttendRequest, $toRevisionRequest, $toRejectRequest, $modalDetailsRequest, $modalDetailsRequests, $actualizarSolicitud;
    public $readyToLoad = false;


    
    public $editMode = false; // Para saber si estamos editando
    
    
    
    

    //public $editRequest = false;  
    //public $solicitud_id; // ID de la solicitud a editar  

    /**
     * Variable for mails0
     */
    public $adjunto, $observacion;
    /**
     * Component filters
     */
    public $statuses, $hospital, $selectedStatus, $selectedHospital;
    public $pacient = '';
    /**
     * Form
     */
    public $solicitud, $paciente, $mensaje;


    public function mount()
    {
        $this->authorize('godson.request.view');
        $this->user = Auth::user();
        $this->statuses = EstadoSolicitud::where('alto_riesgo_obstetrico', true)->get();
        $this->hospitals = Hospital::all();
        $this->isHospital = $this->user->hasRole('Hospital') ?? false;
        $this->createRequest = false;
        $this->modalAttendRequest = false;
        $this->toRevisionRequest = false;
        $this->toRejectRequest = false;
        $this->modalDetailsRequest = false;
        $this->selectedStatus = "Todos";
    }

    public function render()
    {
        if ($this->readyToLoad) {
            if ($this->isHospital) {
                if (!isset($this->user->linkedHospital->hospital->id)) {
                    $solicitudes = [];
                    $this->emit('alertError', 'Para ver las solicitudes debe estar vinculado a un Hospital');
                } else {
                    $solicitudes = SolicitudObstetricia::join('paciente_obstetrica', 'paciente_obstetrica.id', 'solicitud_obstetricia.paciente_obstetrica_id')
                        ->where('paciente_obstetrica.primer_nombre', 'like', "%$this->pacient%")
                        ->with('paciente')
                        ->where('hospital_id', $this->user->linkedHospital->hospital->id)
                        ->whereIn('estado_solicitud_id', $this->selectedStatus == "Todos" ? [1,2,3,4] : [$this->selectedStatus])
                        ->select('solicitud_obstetricia.*')
                        ->paginate(20);
                }
            } else {
                $solicitudes = SolicitudObstetricia::join('paciente_obstetrica', 'paciente_obstetrica.id', 'solicitud_obstetricia.paciente_obstetrica_id')
                    ->where('paciente_obstetrica.primer_nombre', 'like', "%$this->pacient%")
                    ->whereIn('estado_solicitud_id', $this->selectedStatus == "Todos" ? [1,2,3,4] : [$this->selectedStatus])
                    ->whereIn('solicitud_obstetricia.hospital_id', $this->selectedHospital ? [$this->selectedHospital] : $this->hospitals->pluck('id')->toArray())
                    ->with('paciente')
                    ->select('solicitud_obstetricia.*')
                    ->paginate(20);
            }
        } else {
            $solicitudes = [];
        }
        return view('livewire.godson-request-component', compact('solicitudes'));
    }

    protected $rules = [
        'paciente.primer_nombre'        => 'required|string',
        'paciente.segundo_nombre'       => 'string',
        'paciente.primer_apellido'      => 'required|string',
        'paciente.segundo_apellido'     => 'string',
        'paciente.fecha_nacimiento'     => 'required|date',
        'paciente.tipo_documento_id'    => 'required',
        'paciente.numero_documento'     => 'required|numeric|digits_between:5,20',
        'paciente.eps_id'               => 'required',
        'paciente.direccion'            => 'string',
        'paciente.telefono'             => 'numeric|digits_between:7,10',
        'solicitud.especialidad_id'     => 'required',
        'solicitud.documento_identidad' => 'required|file|max:2048',
        'solicitud.historia_clinica'    => 'file|max:2048',
        'solicitud.adjunto_adicional'   => 'file|max:2048',
        'solicitud.observacion'         => 'string:1200',
    ];
    
    public function loadRequests()
    {
        $this->readyToLoad = true;
    }

    public function createRequest()
    {
        $this->authorize('godson.request.make');
        if (!$this->user->linkedHospital)
            return $this->emit('alertError', 'Usted no está vinculado con un Hospital');
        $this->reset(['solicitud', 'paciente']);
        $this->eps = eps::where('estado', true)->orderBy('nombre', 'asc')->get()->toArray();
        //$this->especialidades = servicios::where('servnomb', 'ALTO RIESGO OBSTETRICO')->orderBy('servnomb', 'asc')->get()->toArray();
        $this->especialidades = servicios::orderBy('servnomb', 'asc')->get()->toArray();
        $this->tiposIdentificacion = tipo_identificacion::orderBy('nombre', 'asc')->get()->toArray();
        $this->createRequest = true;
    }

    
    /*// codigo para actualizar un registro Carlos Valderrama_____________________________________________________________________________________________________________________
class SolicitudForm extends Component
{
    public $createRequest = false;
    public $editMode = false; // Para saber si estamos editando
    public $solicitud;
    public $paciente;
    public $tiposIdentificacion;
    public $eps;
    public $especialidades;

    public function mount()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->solicitud = [
            'especialidad_id' => '',
            'historia_clinica' => null,
            'documento_identidad' => null,
            'adjunto_adicional' => null,
            'observacion' => '',
        ];
        $this->paciente = [
            'primer_nombre' => '',
            'segundo_nombre' => '',
            'primer_apellido' => '',
            'segundo_apellido' => '',
            'fecha_nacimiento' => '',
            'tipo_documento_id' => '',
            'numero_documento' => '',
            'eps_id' => '',
            'direccion_residencia' => '',
            'telefono' => '',
        ];
        $this->editMode = false;
    }

    public function registrarSolicitud()
    {
        // Guardar nueva solicitud en la base de datos
        Solicitud::create($this->solicitud);
        $this->resetForm();
        $this->createRequest = false;
    }

    public function editarSolicitud($id)
    {
        // Cargar datos de la solicitud
        $solicitud = Solicitud::findOrFail($id);
        $this->solicitud = $solicitud->toArray();
        $this->paciente = $solicitud->paciente->toArray();

        $this->editMode = true;
        $this->createRequest = true; // Abrir modal
    }

    public function actualizarSolicitud()
    {
        // Buscar la solicitud y actualizar datos
        $solicitud = Solicitud::findOrFail($this->solicitud['id']);
        $solicitud->update($this->solicitud);
        
        $solicitud->paciente->update($this->paciente);

        $this->resetForm();
        $this->createRequest = false;
    }

    public function render()
    {
        return view('livewire.solicitud-form');
    }
}

 

  
  //   codigo para actualizar un registro _________________________________________________________________________________________________________________________________________*/







    public function registrarSolicitud()
    {
        $this->validate();
        try {
            $paciente = PacienteObstetrica::where('numero_documento', $this->paciente['numero_documento'])->first() ??  false;
            if (!$paciente)
                $paciente = $this->registrarPaciente($this->paciente);

            $result = $this->storeRequest($this->solicitud, $paciente->id);
            $this->createRequest = false;
            $this->reset(['solicitud', 'paciente']);
            $this->emit('alertSuccess', 'Solicitud registrada.');
        } catch (\Throwable $th) {
            $this->emit('alertError', env('APP_DEBUG') ? $th->getMessage() : 'Hubo un error al registrar la solicitud');
        }
    }












    private function registrarPaciente($data)
    {
        $paciente = new PacienteObstetrica();
        $paciente->fill($data);
        $paciente->save();
        return $paciente;
    }

    private function storeRequest($data, $pacientId)
    {
        $archivos = $this->storeFiles();
        $request = new SolicitudObstetricia();
        $request->paciente_obstetrica_id = $pacientId;
        $request->user_id = Auth::user()->id;
        if ($archivos['HISTORIA_CLINICA'])
            $request->historia_clinica = $archivos['HISTORIA_CLINICA'];
        if ($archivos['DOCUMENTO_IDENTIDAD'])
            $request->documento_identidad = $archivos['DOCUMENTO_IDENTIDAD'];
        if ($archivos['ADJUNTO_ADICIONAL'])
            $request->historia_clinica = $archivos['ADJUNTO_ADICIONAL'];
        $request->estado_solicitud_id = EstadoSolicitud::ESTADOS["PENDIENTE"];
        $request->especialidad_id = $data['especialidad_id'];
        $request->hospital_id = $this->user->linkedHospital->hospital->id;
        $request->observacion = $data['observacion'];
        $request->save();
        return $request;
    }

    private function storeFiles()
    {
        $pathDocumentoIdentidad = Str::uuid() . '.' . $this->solicitud['documento_identidad']->extension();
        $this->solicitud['documento_identidad']->storeAs("solicitud/", $pathDocumentoIdentidad, 'obstetricia');
        if (isset($this->solicitud['historia_clinica'])) {
            $pathHistoria = Str::uuid() . '.' . $this->solicitud['historia_clinica']->extension();
            $this->solicitud['historia_clinica']->storeAs("solicitud/", $pathHistoria, 'obstetricia');
        }
        if (isset($this->solicitud['adjunto_adicional'])) {
            $pathAdjunto = Str::uuid() . '.' . $this->solicitud['historia_clinica']->extension();
            $this->solicitud['adjunto_adicional']->storeAs("solicitud/", $pathAdjunto, 'obstetricia');
        }
        $response = [
            "HISTORIA_CLINICA" => $pathHistoria ?? null,
            "DOCUMENTO_IDENTIDAD" => $pathDocumentoIdentidad ?? null,
            "ADJUNTO_ADICIONAL" => $pathAdjunto ?? null,
        ];
        return $response;
    }

    public function attendRequestModal(SolicitudObstetricia $solicitudObstetricia, PacienteObstetrica $pacienteObstetrica, $toRevisionRequest = false, $toRejectRequest = null)
    {
        $this->authorize('godson.request.attend');
        $this->reset(['solicitud', 'paciente', 'observacion', 'adjunto']);
        $this->solicitud = $solicitudObstetricia;
        $this->paciente = $pacienteObstetrica;
        if($toRevisionRequest)
            $this->toRevisionRequest = true;
        else 
            $this->toRevisionRequest = false;
        if($toRejectRequest)
            $this->toRejectRequest = true;
        else 
            $this->toRejectRequest = false;
        $this->modalAttendRequest = true;
    }

    public function attendRequest()
    {
        try {
            if ($this->adjunto)
                $this->saveAttachFiles();
            Mail::mailer('telemedicina')->to($this->solicitud->hospital->email)->send(new SolvedRequest($this->paciente, $this->observacion, $this->adjunto));
            $this->solicitud->fecha_atendido = now()->format('Y-m-d H:i:s');
            $this->solicitud->estado_solicitud_id = EstadoSolicitud::ESTADOS["ATENDIDO"];
            $this->solicitud->save();
            $this->reset(['solicitud', 'paciente', 'observacion', 'adjunto']);
        } catch (\Throwable $th) {
            $this->emit('alertError', env('APP_DEBUG') ? $th->getMessage() : 'Hubo un error al registrar la solicitud');
        }
        
    }

    public function saveAttachFiles()
    {
        foreach ($this->adjunto as $key => $file) {
            $pathAdjunto = Str::uuid() . '.' . $file->extension();
            $file->storeAs("files_telemedicina/", $pathAdjunto, 'obstetricia');
        }
    }

    public function downloadFile($filePath)
    {
        try {
            $file = storage_path()."/obstetricia/solicitud/$filePath";
            return response()->download($file);
        } catch (\Throwable $th) {
            $this->emit('alertError', env('APP_DEBUG') ? $th->getMessage() : 'Ocurrió un error al descargar el archivo');
        }
    }

    public function toRevisionRequest()
    {
        $this->authorize('godson.request.check');
        try {
            Mail::mailer('telemedicina')->to($this->solicitud->hospital->email)->send(new OnRevisionRequest($this->paciente, $this->observacion));
            $this->solicitud->estado_solicitud_id = EstadoSolicitud::ESTADOS["EN_REVISION"];
            $this->solicitud->observacion_revision = $this->observacion;
            $this->solicitud->save();
            $this->reset(['solicitud', 'paciente', 'observacion', 'adjunto']);
        } catch (\Throwable $th) {
            $this->emit('alertError', env('APP_DEBUG') ? $th->getMessage() : 'Hubo un error al registrar la solicitud');
        }
        
    }
    public function rejectRequest()
    {
        $this->authorize('godson.request.reject');
        try {
            Mail::mailer('telemedicina')->to($this->solicitud->hospital->email)->send(new RejectedRequest($this->paciente, $this->observacion));
            $this->solicitud->estado_solicitud_id = EstadoSolicitud::ESTADOS["RECHAZADO"];
            $this->solicitud->observacion_rechazo = $this->observacion;
            $this->solicitud->save();
            $this->reset(['solicitud', 'paciente', 'observacion', 'adjunto']);
        } catch (\Throwable $th) {
            $this->emit('alertError', env('APP_DEBUG') ? $th->getMessage() : 'Hubo un error al registrar la solicitud');
        }
        
    }

    public function cancelRequest(SolicitudObstetricia $solicitudObstetricia, PacienteObstetrica $pacienteObstetrica)
    {
        $this->authorize('godson.request.cancel');
        try {
            $this->solicitud = $solicitudObstetricia;
            $this->paciente = $pacienteObstetrica;
            $this->solicitud->estado_solicitud_id = EstadoSolicitud::ESTADOS["CANCELADO"];
            $this->solicitud->save();
            $this->reset(['solicitud', 'paciente', 'observacion', 'adjunto']);
        } catch (\Throwable $th) {
            $this->emit('alertError', env('APP_DEBUG') ? $th->getMessage() : 'Hubo un error al registrar la solicitud');
        }
        
    }

    public function detailsRequestModal(SolicitudObstetricia $solicitudObstetricia, PacienteObstetrica $pacienteObstetrica)
    {
        $this->reset(['solicitud', 'paciente']);
        $this->solicitud = $solicitudObstetricia;
        $this->paciente = $pacienteObstetrica;
        $this->modalDetailsRequest = true;
    }


    public function detailsRequestModals(SolicitudObstetricia $solicitudObstetricia, PacienteObstetrica $pacienteObstetrica)
    {
        $this->reset(['solicitud', 'paciente']);
        $this->solicitud = $solicitudObstetricia;
        $this->paciente = $pacienteObstetrica;
        $this->modalDetailsRequests = true;
    }





}
