<?php

namespace App\Exports;

use App\Http\Livewire\MensajesMasivos\Consulta as MensajesMasivos;
use App\Models\CitacionMasiva;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

/**
 * Informe de confirmaciones de Mensajes Masivos, con los mismos filtros de la pantalla.
 * Todo se escribe como texto: así Excel no muestra el documento en notación científica.
 */
class CitacionesMasivasExport extends StringValueBinder implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithCustomValueBinder
{
    use Exportable;

    private $filtros;

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function query()
    {
        return CitacionMasiva::query()->filtrar($this->filtros)->orderByDesc('created_at')->orderBy('paciente');
    }

    public function headings(): array
    {
        return [
            'FECHA ENVÍO',
            'ARCHIVO',
            'NOMBRE DEL PACIENTE',
            'NUMERO DE DOCUMENTO',
            'CORREO',
            'ESPECIALIDAD',
            'DIA',
            'FECHA DE CITA',
            'HORA DE CITA',
            'NOMBRE ESPECIALISTA',
            'SEDE',
            'RESPUESTA',
            'FECHA RESPUESTA',
        ];
    }

    public function map($citacion): array
    {
        return [
            $citacion->created_at->format('d/m/Y h:i A'),
            $citacion->archivo,
            $citacion->paciente,
            $citacion->documento,
            $citacion->email,
            $citacion->especialidad,
            $citacion->dia,
            $citacion->fecha_cita,
            $citacion->hora,
            $citacion->especialista,
            MensajesMasivos::SEDES[$citacion->sede]['nombre'] ?? $citacion->sede,
            $citacion->estado_texto,
            $citacion->respondido_at ? $citacion->respondido_at->format('d/m/Y h:i A') : '',
        ];
    }
}
