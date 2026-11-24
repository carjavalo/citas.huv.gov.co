<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Pacientes del archivo importado a los que no se les puede enviar la citación,
 * con el motivo de cada uno (sobre todo los que no están registrados en el sistema).
 *
 * Todo se escribe como texto: así Excel no muestra el documento en notación
 * científica. No se usa ShouldAutoSize porque con miles de filas obliga a medir
 * cada celda; los anchos van fijos.
 */
class NoRegistradosExport extends StringValueBinder implements FromArray, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithCustomValueBinder
{
    use Exportable;

    /** Filas del archivo tal como las dejó la lectura del Excel. */
    private $registros;

    public function __construct(array $registros)
    {
        $this->registros = $registros;
    }

    public function array(): array
    {
        return $this->registros;
    }

    public function headings(): array
    {
        return [
            'FILA DEL ARCHIVO',
            'NOMBRE DEL PACIENTE',
            'NUMERO DE DOCUMENTO',
            'ESPECIALIDAD',
            'DIA',
            'FECHA DE CITA',
            'HORA DE CITA',
            'NOMBRE ESPECIALISTA',
            'MOTIVO',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 38,
            'C' => 22,
            'D' => 28,
            'E' => 14,
            'F' => 16,
            'G' => 14,
            'H' => 32,
            'I' => 34,
        ];
    }

    public function styles(Worksheet $hoja)
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    /** Por qué este paciente no recibe la citación. */
    public static function motivo(array $registro)
    {
        if (empty($registro['encontrado'])) {
            return 'No registrado en el sistema';
        }
        if (empty($registro['email'])) {
            return 'Registrado, pero sin correo electrónico';
        }
        if (empty($registro['completo'])) {
            return 'Faltan datos de la cita en el archivo';
        }

        return 'No se pudo enviar';
    }

    public function map($registro): array
    {
        return [
            $registro['fila'],
            // Sin la columna NOMBRE DEL PACIENTE queda el nombre registrado, si lo hay.
            ($registro['paciente'] ?? '') ?: ($registro['nombre'] ?? ''),
            $registro['documento'],
            $registro['especialidad'],
            $registro['dia'],
            $registro['fecha'],
            $registro['hora'],
            $registro['especialista'],
            self::motivo($registro),
        ];
    }
}
