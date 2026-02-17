<?php

namespace App\Http\Livewire\Citas;

use App\Mail\DatosCita;
use App\Mail\CancelarCita;
use App\Mail\ReagendarCita;
use App\Models\cancel_citas;
use App\Models\solicitudes;
use App\Models\User;
use App\Models\Sede;
use App\Models\servicios;
use App\Models\eps;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Carbon\Carbon;

class ConsultaGeneral extends Component
{

    public $modal = false;
    public $detalles = false; //Modal detalles
    public $rechazar = false; //Modal rechazar solicitud
    public $notificar_espera = false; //Modal notificar solicitud en espera
    public $sol_id;
    public $filestado, $filserv, $filpaciente, $fileps, $filsede = ''; //Filtros de búsqueda
    public $selectedSolicitudes = []; // Para selección múltiple de solicitudes
    public $selectAll = false; // Para seleccionar todos
    public $sortField = 'solicitudes.id'; // Campo de ordenamiento por defecto
    public $sortDirection = 'desc'; // Dirección de ordenamiento por defecto
    public $fecha, $hora, $reserva, $correo, $solicitud, $usu_nomb, $ndocumento, $tipo_documento, $contacto, $pacid, $solnum, $archivos, $observacion, $codigo_autorizacion;
    public $ubicacion = "Calle. 5 # 36 - 08, Barrio San Fernando Cali, Valle del Cauca";
    public $hoy;
    public $mensaje; //Mensaje del consultor al usuario
    public $adjunto;
    
    // Cache de datos para optimizar rendimiento
    private $sedesCache = null;
    private $especialidadesCache = null;
    private $aseguradoresCache = null;
    private $lastSedeFilter = null;

    use AuthorizesRequests;
    use WithPagination;
    use WithFileUploads;

    protected $rules = [
        'fecha'         => 'required',
        'hora'          => 'required',
        'ubicacion'     => 'required',
        'adjunto.0'       => 'required|mimes:pdf',
        'adjunto.1'     => 'mimes:pdf',
        'adjunto.2'     => 'mimes:pdf',
        'reserva'       => 'required',
    ];

    protected $messages = [
        'fecha.required'        => 'Este campo no puede estar vacío.',
        'hora.required'         => 'Este campo no puede estar vacío.',
        'ubicacion.required'    => 'Seleccione la ubicación.',
        'adjunto.*.required'    => 'Adjunte el certificado generado.',
        'adjunto.*.mimes'       => 'Solo se admiten archivos PDF',
        'reserva.required'      => 'Este campo no puede estar vacío.',
    ];

    protected $listeners = ['render','cerrarDetalles','cerrarRechazar','cerrarNotificarEspera'];

    public function mount()
    {
        $this->authorize('citas.consulta.agendar');
        $this->filestado = 'Pendiente';
    }

    /**
     * Limpia el caché de datos para forzar recarga
     * Útil después de cambios en filtros importantes
     */
    private function clearCache()
    {
        $this->sedesCache = null;
        $this->especialidadesCache = null;
        $this->aseguradoresCache = null;
        $this->lastSedeFilter = null;
    }

    public function render()
    {
        // Optimización: Usar índices con condiciones específicas
        $query = solicitudes::where('solicitudes.estado', $this->filestado)
            ->join('users', 'solicitudes.pacid', '=', 'users.id')
            ->join('eps', 'users.eps', '=', 'eps.id')
            ->join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
            ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
            ->join('sedes', 'pservicios.sede_id', '=', 'sedes.id');
        
        // Aplicar filtros solo si tienen valor (evitar búsquedas LIKE innecesarias)
        if (!empty($this->filserv)) {
            $query->where('servicios.servnomb', 'like', '%' . $this->filserv . '%');
        }
        if (!empty($this->filpaciente)) {
            $query->where('users.ndocumento', 'like', '%' . $this->filpaciente . '%');
        }
        if (!empty($this->fileps)) {
            $query->where('eps.nombre', 'like', '%' . $this->fileps . '%');
        }
        if ($this->filsede !== '') {
            $query->where('sedes.id', $this->filsede);
        }

        // Restricción de visibilidad según rol del usuario
        $user = Auth::user();
        
        // Super Admin ve todo sin restricciones
        // Administrador, Coordinador y Consultor: filtran por sede y servicio (pservicio)
        if (!$user->hasRole('Super Admin')) {
            if ($user->sede_id) {
                $query->where('sedes.id', $user->sede_id);
            }
            if ($user->pservicio_id) {
                $query->where('pservicios.id', $user->pservicio_id);
            }
        }
        
        // Seleccionar solo campos necesarios para mejorar velocidad
        $solicitudes = $query->orderBy($this->sortField, $this->sortDirection)
            ->select([
                'solicitudes.id',
                'solicitudes.solnum',
                'solicitudes.estado',
                'solicitudes.created_at',
                'solicitudes.espec',
                'users.id as user_id',
                'users.name',
                'users.email',
                'users.apellido1',
                'users.ndocumento',
                'eps.nombre as eps_nombre',
                'servicios.servnomb',
                'sedes.nombre as sede_nombre',
            ])
            ->paginate(10);
        
        // Cachear datos que no cambian frecuentemente
        if ($this->sedesCache === null) {
            $this->sedesCache = Sede::where('estado', true)->orderBy('nombre', 'asc')->get();
        }
        $sedes = $this->sedesCache;
        
        // Cachear especialidades solo cuando cambia el filtro de sede
        if ($this->especialidadesCache === null || $this->lastSedeFilter !== $this->filsede) {
            if ($this->filsede !== '' && $this->filsede !== null) {
                $this->especialidadesCache = servicios::where('estado', 1)
                    ->whereHas('pservicio', function($q) {
                        $q->where('sede_id', $this->filsede);
                    })
                    ->orderBy('servnomb', 'asc')
                    ->get();
            } else {
                $this->especialidadesCache = servicios::where('estado', 1)
                    ->orderBy('servnomb', 'asc')
                    ->get();
            }
            $this->lastSedeFilter = $this->filsede;
        }
        $especialidades = $this->especialidadesCache;
        
        // Cachear EPS
        if ($this->aseguradoresCache === null) {
            $this->aseguradoresCache = eps::orderBy('nombre', 'asc')->get();
        }
        $aseguradoras = $this->aseguradoresCache;
        
        return view('livewire.citas.consulta-general',[
            'solicitudes' => $solicitudes,
            'sedes' => $sedes,
            'especialidades' => $especialidades,
            'aseguradoras' => $aseguradoras,
        ]);
    }

    public function updatingFilserv()
    {
        $this->resetPage();
    }

    public function updatingFilestado()
    {
        $this->clearCache(); // Limpiar caché cuando cambia el filtro de estado
        $this->resetPage();
    }
    public function updatingFileps()
    {
        $this->resetPage();
    }

    public function updatingFilsede()
    {
        $this->clearCache(); // Limpiar caché cuando cambia la sede
        $this->resetPage();
        $this->filserv = ''; // Limpiar filtro de especialidad al cambiar sede
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function agendar($solicitud_id = null)
    {
        $this->authorize('citas.consulta.agendar');
        if (empty($solicitud_id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud. Por favor, recargue la página e intente nuevamente.');
            return;
        }
        try {
            $this->solicitud = $solicitud_id;
            
            // Optimización: Consulta más rápida usando first() en lugar de get()
            // Solo se hacen los joins necesarios y se seleccionan los campos requeridos
            $datos = solicitudes::where('solicitudes.id', $this->solicitud)
                ->join('users', 'solicitudes.pacid', '=', 'users.id')
                ->leftJoin('tipo_identificacions', 'users.tdocumento', '=', 'tipo_identificacions.id')
                ->select([
                    'solicitudes.pacid as paciente_id',
                    'users.name as paciente_nombres',
                    'users.apellido1 as paciente_apellido1',
                    'users.email',
                    'users.telefono1 as paciente_telefono1',
                    'users.ndocumento as paciente_numero_documento',
                    \Illuminate\Support\Facades\DB::raw("COALESCE(tipo_identificacions.nombre, 'Sin tipo') as paciente_tipo_documento"),
                    'solicitudes.solnum',
                    'solicitudes.pacdocid',
                    'solicitudes.pacauto',
                    'solicitudes.pacordmed',
                    'solicitudes.pachis',
                    'solicitudes.pacobs',
                    'solicitudes.codigo_autorizacion',
                    'solicitudes.estado',
                    'solicitudes.soporte_patologia',
                ])
                ->first(); // Cambiar get() por first() para obtener un solo registro
            
            if (!$datos) {
                $this->emit('alertError', 'No se encontró la solicitud o cambió de estado.');
                return;
            }
            
            // Asignar datos al componente (mostrar modal rápidamente)
            $this->usu_nomb         = $datos->paciente_nombres.' '.$datos->paciente_apellido1;
            $this->correo           = $datos->email;
            $this->ndocumento       = $datos->paciente_numero_documento;
            $this->pacid            = $datos->paciente_id;
            $this->solnum           = $datos->solnum;
            $this->observacion      = $datos->pacobs;
            $this->tipo_documento   = $datos->paciente_tipo_documento;
            $this->contacto         = $datos->paciente_telefono1;
            $this->archivos = [
                'documento' => $datos->pacdocid,
                'historia'  => $datos->pachis,
                'autorizacion'  => $datos->pacauto,
                'orden'     => $datos->pacordmed,
                'soporte_patologia' => $datos->soporte_patologia,
            ];
            $this->codigo_autorizacion = $datos->codigo_autorizacion;
            $this->hoy = Carbon::now()->format('Y-m-d');
            
            // Abrir modal inmediatamente (sin esperar la actualización de estado)
            $this->abrirModal();
            
            // Actualizar el estado de forma asíncrona en background
            $estado_anterior = $datos->estado;
            solicitudes::where('id', $this->solicitud)->update([
                'estado' => 'Procesando',
                'usercod' => Auth::user()->id,
                'estado_anterior' => $estado_anterior,
            ]);
            
        } catch (\Throwable $th) {
            $this->emit('alertError','Ocurrió un error: '.$th->getMessage());
        }
    }

    public function abrirModal()
    {
        $this->modal = true;
    }

    public function cerrarModal()
    {
        $this->resetExcept(['filestado','filserv','filpaciente', 'fileps']);
        $this->modal = false;
    }

    public function cancelar()
    {
        $solicitud = solicitudes::where('id', $this->solicitud)->first();
        if (!$solicitud) {
            $this->emit('alertError', 'No se encontró la solicitud.');
            $this->modal = false;
            return;
        }
        switch ($solicitud->estado_anterior) {
            case 'Espera':
                $solicitud->update([
                    'estado'            => 'Espera',
                    'estado_anterior'   => null,
                    'usercod'           => Auth::user()->id]);
                break;

            case 'Pendiente':
                $solicitud->update([
                'estado'            => 'Pendiente',
                'estado_anterior'   => null,
                'usercod'           => Auth::user()->id]);
                break;
        }
        $this->resetExcept(['filestado','filserv','fileps']);
        $this->emitSelf('render');
        $this->modal = false;
    }

    public function cita()
    {
        $this->authorize('citas.consulta.agendar');
        $this->validate();
        $ruta = 'Documentos/usuario'.$this->pacid.'/solicitud_'.$this->solnum.'/';  //Se almacena la ruta de la solicitud

        try {
            $paciente = User::where('id','=',$this->pacid)->get(['name','apellido1','apellido2'])->first();
            $datos_paciente = $paciente->name.' '.$paciente->apellido1.' '.$paciente->apellido2;
            foreach($this->adjunto as $archivo){
                $archivo->storeAs('Documentos/usuario'.$this->pacid.'/solicitud_'.$this->solnum,$archivo->getClientOriginalName(), 'upload');
            }
            Mail::to($this->correo)->send(new DatosCita($this->fecha, $this->hora, $this->ubicacion, $this->adjunto, $this->reserva, $datos_paciente, $this->mensaje, $ruta)); //SE ENVÍA CORREO CON LOS DATOS DE LA CITA
            solicitudes::where('id', $this->solicitud)->update([
                'estado'                                    => 'Agendado',
                'usercod'                                   => Auth::user()->id,
                'certfdo_cita'                              => 'Documentos/usuario'.$this->pacid.'/solicitud_'.$this->solnum.'/'.$this->adjunto[0]->getClientOriginalName(),
                'solicitud_mensaje_agendamiento'            => $this->mensaje,
            ]);
            $this->cerrarModal();
            $this->resetExcept(['filestado','filserv','aseguradoras']);
            $this->emit('alertSuccess','Notificación de cita enviada satisfactoriamente.'); //Evento para emitir alerta

        } catch (\Throwable $th) {
            $this->emit('alertError','Ocurrió un error'.$th); //Evento para emitir alerta de error
        }
    }

    public function cancelarCita($id = null)
    {
        if (empty($id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud. Por favor, recargue la página e intente nuevamente.');
            return;
        }
        try {
            $solicitud = solicitudes::join('users', 'solicitudes.pacid', '=', 'users.id')
                ->join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
                ->where('solicitudes.id', $id)
                ->select(['solicitudes.*', 'users.name', 'users.apellido1', 'users.apellido2', 'users.email', 'servicios.servnomb'])
                ->first();
            
            solicitudes::where('id', $id)->update(['estado' => 'Cancelado', 'usercod' => Auth::user()->id]);
            cancel_citas::create([
                'user_id'   => Auth::user()->id,
                'solicitud_id'  => $id,
            ]);
            
            $usuario = $solicitud->name . ' ' . $solicitud->apellido1 . ' ' . $solicitud->apellido2;
            Mail::to($solicitud->email)->send(new CancelarCita($usuario, $solicitud->servnomb));
            $this->emit('alertSuccess', 'Cita cancelada y notificación enviada al correo ' . $solicitud->email);
        } catch (\Throwable $th) {
            $this->emit('alertError', 'Error al cancelar: ' . $th->getMessage());
        }
    }

    public function detalles($sol_id = null)
    {
        if (empty($sol_id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud. Por favor, recargue la página e intente nuevamente.');
            return;
        }
        $this->sol_id = $sol_id;
        $this->detalles = true;
    }

    public function cerrarDetalles()
    {
        $this->detalles = false;
    }

    public function rechazar($sol_id = null)
    {
        if (empty($sol_id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud. Por favor, recargue la página e intente nuevamente.');
            return;
        }
        $this->sol_id = $sol_id;
        $this->rechazar = true;
    }

    public function cerrarRechazar()
    {
        $this->rechazar = false;
    }

    public function cambiarEstado($sol_id = null) //Función para mitigar error cuando se refresca la ventana con una solicitud en agendamiento
    {
        if (empty($sol_id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud. Por favor, recargue la página e intente nuevamente.');
            return;
        }
        $solicitud = solicitudes::where('id', $sol_id)->first();
        $estado_anterior = $solicitud->estado_anterior; //Toma el estado anterior para regresarlo
        solicitudes::where('id', $sol_id)->update([
            'estado' => $estado_anterior,
            'estado_anterior'   => null,
            'usercod'   => Auth::user()->id,
        ]);
    }

    public function notificarEspera($sol_id = null)
    {
        if (empty($sol_id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud. Por favor, recargue la página e intente nuevamente.');
            return;
        }
        $this->sol_id = $sol_id;
        $this->notificar_espera = true;
    }

    public function cerrarNotificarEspera()
    {
        $this->notificar_espera = false;
    }

    public function reagendarCita($sol_id = null) //SOLUCIÓN RÁPIDA PARA CAMBIAR EL ESTADO DE UNA CITA EN ESTADO "ESPERA" A "PENDIENTE"
    {
        if (empty($sol_id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud. Por favor, recargue la página e intente nuevamente.');
            return;
        }
        try {
            $solicitud = solicitudes::join('users', 'solicitudes.pacid', '=', 'users.id')
                ->join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
                ->where('solicitudes.id', $sol_id)
                ->select(['solicitudes.*', 'users.name', 'users.apellido1', 'users.apellido2', 'users.email', 'servicios.servnomb'])
                ->first();
            
            solicitudes::where('id', $sol_id)->update([
                'estado' => 'Pendiente',
                'usercod'   => Auth::user()->id,
            ]);
            
            $usuario = $solicitud->name . ' ' . $solicitud->apellido1 . ' ' . $solicitud->apellido2;
            Mail::to($solicitud->email)->send(new ReagendarCita($usuario, $solicitud->servnomb));
            $this->emit('alertSuccess', 'Solicitud reagendada y notificación enviada al correo ' . $solicitud->email);
        } catch (\Throwable $th) {
            $this->emit('alertError', 'Error al reagendar: ' . $th->getMessage());
        }
    }

    // Eliminar una solicitud individual (Solo Super Admin)
    public function eliminarSolicitud($id = null)
    {
        if (empty($id)) {
            $this->emit('alertError', 'No se pudo identificar la solicitud. Por favor, recargue la página e intente nuevamente.');
            return;
        }
        if (!Auth::user()->hasRole('Super Admin')) {
            $this->emit('alertError', 'No tiene permisos para eliminar solicitudes.');
            return;
        }
        
        try {
            solicitudes::where('id', $id)->delete();
            $this->emit('alertSuccess', 'Solicitud eliminada correctamente.');
        } catch (\Throwable $th) {
            $this->emit('alertError', 'Error al eliminar: ' . $th->getMessage());
        }
    }

    // Eliminar solicitudes seleccionadas (Solo Super Admin)
    public function eliminarSeleccionados()
    {
        if (!Auth::user()->hasRole('Super Admin')) {
            $this->emit('alertError', 'No tiene permisos para eliminar solicitudes.');
            return;
        }
        
        if (empty($this->selectedSolicitudes)) {
            $this->emit('alertError', 'No hay solicitudes seleccionadas.');
            return;
        }
        
        try {
            $count = count($this->selectedSolicitudes);
            solicitudes::whereIn('id', $this->selectedSolicitudes)->delete();
            $this->selectedSolicitudes = [];
            $this->selectAll = false;
            $this->emit('alertSuccess', $count . ' solicitud(es) eliminada(s) correctamente.');
        } catch (\Throwable $th) {
            $this->emit('alertError', 'Error al eliminar: ' . $th->getMessage());
        }
    }

    // Alternar selección de una solicitud individual
    public function toggleSelection($id)
    {
        $id = (string) $id;
        if (in_array($id, $this->selectedSolicitudes)) {
            $this->selectedSolicitudes = array_values(array_diff($this->selectedSolicitudes, [$id]));
        } else {
            $this->selectedSolicitudes[] = $id;
        }
    }

    // Seleccionar/Deseleccionar todos (solo los visibles en la página actual)
    public function updatedSelectAll($value)
    {
        // Construir la misma consulta que en render() para obtener los IDs visibles
        $query = solicitudes::join('users', 'solicitudes.pacid','=','users.id')
            ->join('eps','users.eps','=','eps.id')
            ->join('servicios','solicitudes.espec','=','servicios.servcod')
            ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
            ->join('sedes', 'pservicios.sede_id', '=', 'sedes.id')
            ->where([['solicitudes.estado','=',$this->filestado],['servnomb','like','%'.$this->filserv.'%'],['users.ndocumento','like','%'.$this->filpaciente.'%'],['eps.nombre','like','%'.$this->fileps.'%']]);
        
        if ($this->filsede !== '') {
            $query->where('sedes.id', $this->filsede);
        }

        // Restricción de visibilidad según rol del usuario
        $user = Auth::user();
        if (!$user->hasRole('Super Admin')) {
            if ($user->sede_id) {
                $query->where('sedes.id', $user->sede_id);
            }
            if ($user->pservicio_id) {
                $query->where('pservicios.id', $user->pservicio_id);
            }
        }
        
        // Obtener IDs de los registros visibles en la página actual (máximo 10)
        $visibleIds = $query->orderBy('solicitudes.created_at','asc')
            ->limit(10)
            ->pluck('solicitudes.id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        if ($value) {
            // Agregar los IDs visibles sin eliminar selecciones previas
            foreach ($visibleIds as $id) {
                if (!in_array($id, $this->selectedSolicitudes)) {
                    $this->selectedSolicitudes[] = $id;
                }
            }
        } else {
            // Solo eliminar los IDs visibles, preservando las demás selecciones
            $this->selectedSolicitudes = array_values(array_diff($this->selectedSolicitudes, $visibleIds));
        }
    }
}
