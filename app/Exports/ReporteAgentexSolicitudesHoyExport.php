<?php

namespace App\Exports;

use App\Models\solicitudes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReporteAgentexSolicitudesHoyExport implements FromQuery, WithHeadings, WithColumnWidths
{
    use Exportable;

    public function __construct($hoy, $mañana)
    {
        $this->hoy= $hoy;
        $this->mañana = $mañana;
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
            'ID SOLICITUD',
            'PACIENTE',
            'APELLIDO 1',
            'APELLIDO 2',
            'TIPO IDENTIFICACION',
            'NUMERO IDENTIFICACION',
            'EPS',
            'FECHA DE SOLICITUD',
            'ESPECIALIDAD',
            'ESTADO',
            'FECHA DE ACTUALIZACION',
            'ID AGENTE',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 35,
            'F' => 35,
            'G' => 35,
            'H' => 35,  
            'I' => 50,
            'J' => 30,
            'K' => 35,
            'L' => 15,          
        ];
    }

    public function query()
    {
        $query = solicitudes::query()->where('solicitudes.estado','<>','Pendiente')
                ->join('users', 'solicitudes.pacid', '=', 'users.id')
                ->join('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
                ->join('eps','users.eps','=','eps.id')
                ->join('tipo_identificacions','users.tdocumento','=','tipo_identificacions.id')
                ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
                ->join('sedes', 'pservicios.sede_id', '=', 'sedes.id')
                ->where([
                    ['solicitudes.updated_at','>=',''.$this->hoy.''],
                    ['solicitudes.updated_at','<',''.$this->mañana.'']
            ]);
        
        // Aplicar filtros de visibilidad según rol
        $this->aplicarFiltrosVisibilidad($query);
        
        return $query->select([
                'solicitudes.id',
                DB::raw('users.name as paciente_nombre'),
                DB::raw('users.apellido1 as paciente_apellido1'),
                DB::raw('users.apellido2 as paciente_apellido2'),
                DB::raw('tipo_identificacions.nombre as paciente_tipo_documento'),
                DB::raw('users.ndocumento as paciente_numero_documento'),
                DB::raw('eps.nombre as eps'),
                'solicitudes.created_at',
                'servicios.servnomb',
                'solicitudes.estado',
                'solicitudes.updated_at',
                'solicitudes.usercod',               
            ]);
    }
}
