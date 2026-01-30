<?php

namespace App\Http\Livewire;

use App\Model\User;
use App\Models\servicios;
use App\Models\solicitudes;
use App\Models\Sede;
use App\Models\Pservicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\CitasExport;

class FilterReporteCitas extends Component
{
    //public $reporteCitas;
    use WithPagination;

    public $filters=[
        //'estado'=>'',
        'fromDate'=>'',
        'toDate'=>'',
    ];

    // Filtros adicionales para Super Admin
    public $filSede = '';
    public $filServicio = '';
    public $filEspecialidad = '';

    public function generateReport(){
        return Excel::download(new CitasExport($this->filters), 'Reporte_Citas.xlsx'); 
   

        
    }
//----------------------------------------------------------------------------------------------------------------
   /* public function render()
    {
       $solicitudes = solicitudes::query()->join('users', 'solicitudes.pacid', '=', 'users.id')->   
       join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')-> 
       join('eps','users.eps','=','eps.id')->               
                       where([['solicitudes.estado','=','Pendiente']
                              ])->orwhere([['solicitudes.estado','=','Espera']
                              ])->select([
                                          DB::raw('users.name as paciente_nombre'),
                                          DB::raw('users.apellido1 as paciente_apellido1'),
                                          DB::raw('users.apellido2 as paciente_apellido2'),
                                          DB::raw('users.ndocumento as paciente_ndocumento'),
                                          DB::raw('servicios.servnomb as servicio_nombre'),
                                          DB::raw('eps.id as codigo_eps'),
                                          DB::raw('eps.nombre as eps'),
                                          'solicitudes.espec',
                                          'solicitudes.estado',
                                          'solicitudes.created_at',
                                           ])->paginate(10);                                       

    // $solicitudes = solicitudes::filter($this->filters)->paginate(12);

      // $solicitudes = solicitudes::paginate(12);
          
        return view('livewire.filter-reporte-citas', compact('solicitudes'));
    }*/

//---------------------------------------------------------------------------------------------------------------------


 
public function render()
    {
            $query = solicitudes::query()->join('users', 'solicitudes.pacid', '=', 'users.id')
                ->join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
                ->join('eps','users.eps','=','eps.id')
                ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
                ->join('sedes', 'pservicios.sede_id', '=', 'sedes.id')
                ->where(function($q) {
                    $q->where([
                        ['solicitudes.created_at','>=', Carbon::parse($this->filters['fromDate'])->format('Y-m-d')],
                        ['solicitudes.created_at','<=', Carbon::parse($this->filters['toDate'])->format('Y-m-d')],
                        ['solicitudes.estado','=','Espera'],
                    ])
                    ->orWhere([
                        ['solicitudes.created_at','>=', Carbon::parse($this->filters['fromDate'])->format('Y-m-d')],
                        ['solicitudes.created_at','<=', Carbon::parse($this->filters['toDate'])->format('Y-m-d')],
                        ['solicitudes.estado','=','Agendado'],
                    ]);
                });

            // Restricción de visibilidad según rol del usuario
            // Super Admin ve todo. Administrador, Coordinador y Consultor filtran por sede y pservicio.
            $user = Auth::user();
            
            if ($user->hasRole('Super Admin')) {
                // Filtros adicionales para Super Admin
                if ($this->filSede !== '') {
                    $query->where('sedes.id', $this->filSede);
                }
                if ($this->filServicio !== '') {
                    $query->where('pservicios.id', $this->filServicio);
                }
                if ($this->filEspecialidad !== '') {
                    $query->where('servicios.servcod', $this->filEspecialidad);
                }
            } else {
                if ($user->sede_id) {
                    $query->where('sedes.id', $user->sede_id);
                }
                if ($user->pservicio_id) {
                    $query->where('pservicios.id', $user->pservicio_id);
                }
            }

            $solicitudes = $query->select([
                DB::raw('users.name as paciente_nombre'),
                DB::raw('users.apellido1 as paciente_apellido1'),
                DB::raw('users.apellido2 as paciente_apellido2'),
                DB::raw('users.ndocumento as paciente_ndocumento'),
                DB::raw('servicios.servnomb as servicio_nombre'),
                DB::raw('eps.id as codigo_eps'),
                DB::raw('eps.nombre as eps'),
                'solicitudes.espec',
                'solicitudes.estado',
                'solicitudes.created_at',
                'solicitudes.updated_at',
            ])->orderBy('solicitudes.created_at','desc')->paginate(12);
            
            // Cargar datos para los filtros de Super Admin
            $sedes = Sede::where('estado', true)->orderBy('nombre')->get();
            $pservicios = Pservicio::orderBy('descripcion')->get();
            $especialidades = servicios::where('estado', 1)->orderBy('servnomb')->get();
          
        return view('livewire.filter-reporte-citas', compact('solicitudes', 'sedes', 'pservicios', 'especialidades'));
    }


    
}
