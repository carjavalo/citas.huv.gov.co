<?php

namespace App\Exports;

use App\Models\solicitudes;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use Maatwebsite\Excel\Excel;
class CitasExport implements FromQuery, WithHeadings, WithColumnWidths, WithCustomStartCell
{
    use Exportable;
    private $filters;
   

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Aplica filtros de visibilidad según rol del usuario
     * Super Admin ve todo. Administrador, Coordinador y Consultor filtran por sede y pservicio.
     */
    private function aplicarFiltrosVisibilidad($query)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('Super Admin')) {
            if ($user->sede_id) {
                $query->where('sedes.id', $user->sede_id);
            }
            if ($user->pservicio_id) {
                $query->where('pservicios.id', $user->pservicio_id);
            }
        }
        
        return $query;
    }

    public function headings(): array
    {
        return [
            'COD. ESPECIALIDAD',
            'SERVICIO',
            'ESTADO',
            'FECHA SOLICITUD',
            'FECHA TRAMITE',
            'NOMBRE PACIENTE',
            'APELLIDO',
            'APELLIDO',
            'N IDENTIFICACION',
            'CODIGO EPS',
            'EPS',
            
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 16,
            'D' => 16,
            'E' => 15,
            'F' => 25,
            'G' => 25,
            'H' => 25,  
            'I' => 18,
            'J' => 12,
            'K' => 35,
                        
        ];
    }

    public function startCell(): string
    {
        return 'A5';
    
    }







    /**
    * @return \Illuminate\Support\Collection
    */
    




    public function query()
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
        
        // Aplicar filtros de visibilidad según rol
        $this->aplicarFiltrosVisibilidad($query);
        
        return $query->select([
            'solicitudes.espec', 
            DB::raw('servicios.servnomb as servicio_nombre'),
            'solicitudes.estado',     
            'solicitudes.created_at',
            'solicitudes.updated_at',
            DB::raw('users.name as paciente_nombre'),
            DB::raw('users.apellido1 as paciente_apellido1'),
            DB::raw('users.apellido2 as paciente_apellido2'),
            DB::raw('users.ndocumento as paciente_ndocumento'),
            DB::raw('eps.id as codigo_eps'),
            DB::raw('eps.nombre as eps'),                                                                                
        ])->orderBy('solicitudes.created_at','desc');
    }



    /*public function map($dato): array
    {
        //dd($dato);
        return [
            $invoice->invoice_number,
            $invoice->user->name,
            Date::dateTimeToExcel($invoice->created_at),
        ];
    }*/



    

    



    /*public function collection()
    {
        return solicitudes::all();
    }*/
}
