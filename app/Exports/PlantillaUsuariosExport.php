<?php

namespace App\Exports;

use App\Support\LectorUsuariosExcel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

/**
 * Archivo de ejemplo para la importación de usuarios: los encabezados exactos
 * que espera el módulo y una fila de muestra.
 */
class PlantillaUsuariosExport extends StringValueBinder implements FromArray, WithHeadings, ShouldAutoSize, WithCustomValueBinder
{
    use Exportable;

    /** Fila de muestra, en el mismo orden que los encabezados. */
    const EJEMPLO = [
        'nombre'         => 'MARIA FERNANDA',
        'apellido_1'     => 'GOMEZ',
        'apellido_2'     => 'RESTREPO',
        'identificacion' => '1144123456',
        'telefono_1'     => '3151234567',
        'telefono_2'     => '6025551234',
        'correo_2'       => 'maria.gomez@example.com',
        'responsable'    => 'COOMEVA EPS',
    ];

    public function headings(): array
    {
        // La primera variante de cada columna es el encabezado documentado.
        return array_map(function ($variantes) {
            return $variantes[0];
        }, array_values(LectorUsuariosExcel::COLUMNAS));
    }

    public function array(): array
    {
        $fila = [];
        foreach (array_keys(LectorUsuariosExcel::COLUMNAS) as $clave) {
            $fila[] = self::EJEMPLO[$clave] ?? '';
        }

        return [$fila];
    }
}
