<?php

namespace App\Exports;

use App\Models\UserActivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ActividadesUsuariosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $tipoActividad;

    public function __construct($fechaInicio, $fechaFin, $tipoActividad = '')
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->tipoActividad = $tipoActividad;
    }

    public function collection()
    {
        $query = UserActivity::with('user')
            ->whereBetween('created_at', [
                $this->fechaInicio . ' 00:00:00',
                $this->fechaFin . ' 23:59:59'
            ])
            ->whereHas('user', function($q) {
                // Excluir Super Admin
                $q->whereDoesntHave('roles', function($roleQuery) {
                    $roleQuery->where('name', 'Super Admin');
                });
            });

        // Aplicar filtro de tipo si está seleccionado
        if ($this->tipoActividad && $this->tipoActividad !== '') {
            $query->where('tipo_actividad', $this->tipoActividad);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Documento',
            'Usuario',
            'Email',
            'Fecha',
            'Hora',
            'Tipo de Actividad',
            'Descripción',
            'Módulo',
            'Acción',
            'Dirección IP',
        ];
    }

    public function map($actividad): array
    {
        $usuario = $actividad->user;
        
        return [
            $usuario ? $usuario->ndocumento : 'N/A',
            $usuario ? $usuario->name . ' ' . $usuario->apellido1 . ' ' . $usuario->apellido2 : 'Usuario Eliminado',
            $usuario ? $usuario->email : 'N/A',
            Carbon::parse($actividad->created_at)->format('d/m/Y'),
            Carbon::parse($actividad->created_at)->format('H:i:s'),
            $this->getTipoActividadTexto($actividad->tipo_actividad),
            $actividad->descripcion,
            $actividad->modulo ?? '-',
            $actividad->accion ?? '-',
            $actividad->ip_address ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila de encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2e3a75'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Actividades de Usuarios';
    }

    private function getTipoActividadTexto($tipo)
    {
        $tipos = [
            'login' => 'Ingreso',
            'logout' => 'Salida',
            'registro' => 'Registro',
            'cita' => 'Cita',
            'accion' => 'Acción',
        ];

        return $tipos[$tipo] ?? $tipo;
    }
}
