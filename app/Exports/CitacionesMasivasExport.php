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
        return CitacionMasiva::query()->filtrar($this->filtros)->orderByDesc('enviado_at')->orderBy('paciente')->orderBy('id');
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
            'CORREO ENVIADO',
            'FECHA ENVÍO DEL CORREO',
            'RESPUESTA',
            'FECHA RESPUESTA',
        ];
    }

    /** 'FECHA ENVÍO' es cuándo se encoló; esto es cuándo salió el correo de verdad. */
    private function estadoEntrega($citacion)
    {
        if ($citacion->entregado_at) {
            return 'Sí';
        }
        if ($citacion->estado === CitacionMasiva::FALLIDO) {
            return 'No (no entregado)';
        }
        if ($citacion->enviado_at && $citacion->enviado_at->format('Y-m-d') >= CitacionMasiva::SEGUIMIENTO_DESDE) {
            return 'Aún en la cola';
        }

        return 'Sin dato';
    }

    public function map($citacion): array
    {
        return [
            ($citacion->enviado_at ?? $citacion->created_at)->format('d/m/Y h:i A'),
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
            $this->estadoEntrega($citacion),
            $citacion->entregado_at ? $citacion->entregado_at->format('d/m/Y h:i A') : '',
            $citacion->estado_texto,
            $citacion->respondido_at ? $citacion->respondido_at->format('d/m/Y h:i A') : '',
        ];
    }
}
