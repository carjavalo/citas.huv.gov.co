<?php

namespace App\Exports;

use App\Models\ImportacionUsuarioDetalle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

/**
 * Detalle completo de una importación de usuarios.
 * Todo se escribe como texto: así Excel no muestra la identificación ni los
 * teléfonos en notación científica.
 */
class ImportacionUsuariosExport extends StringValueBinder implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithCustomValueBinder
{
    use Exportable;

    private $importacionId;

    public function __construct($importacionId)
    {
        $this->importacionId = (int) $importacionId;
    }

    public function query()
    {
        return ImportacionUsuarioDetalle::where('importacion_id', $this->importacionId)
            ->orderBy('fila')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'FILA DEL ARCHIVO',
            'RESULTADO',
            'MOTIVO',
            'IDENTIFICACION',
            'NOMBRE',
            'APELLIDO_1',
            'APELLIDO_2',
            'CORREO',
            'TELEFONO_1',
            'TELEFONO_2',
            'RESPONSABLE (ARCHIVO)',
            'EPS ASIGNADA',
            // En los importados es el usuario nuevo; en los que ya existían, el
            // usuario que tenía esa identificación o ese correo.
            'ID DE USUARIO',
        ];
    }

    public function map($detalle): array
    {
        return [
            $detalle->fila,
            $detalle->estado_texto,
            $detalle->motivo,
            $detalle->ndocumento,
            $detalle->nombre,
            $detalle->apellido1,
            $detalle->apellido2,
            $detalle->email,
            $detalle->telefono1,
            $detalle->telefono2,
            $detalle->responsable,
            $detalle->eps_nombre ?: ($detalle->responsable ? 'Sin coincidencia' : ''),
            $detalle->user_id,
        ];
    }
}
