<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SqlQueryExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $results;
    protected $columns;

    public function __construct($results, $columns)
    {
        $this->results = $results;
        $this->columns = $columns;
    }

    public function collection()
    {
        $data = [];
        
        foreach ($this->results as $row) {
            // Convert row to object if it's an array
            $row = (object) $row;
            $rowData = [];
            foreach ($this->columns as $column) {
                $value = $row->$column ?? null;
                
                // Handle different data types
                if (is_null($value)) {
                    $rowData[] = 'NULL';
                } elseif (is_bool($value)) {
                    $rowData[] = $value ? 'TRUE' : 'FALSE';
                } elseif (is_array($value) || is_object($value)) {
                    $rowData[] = json_encode($value);
                } else {
                    $rowData[] = $value;
                }
            }
            $data[] = $rowData;
        }
        
        return collect($data);
    }

    public function headings(): array
    {
        return $this->columns;
    }

    public function title(): string
    {
        return 'SQL Query Results';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2c4370'],
                ],
            ],
            // Auto-size columns for better readability
            'A' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'B' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'C' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'D' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'E' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'F' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'G' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'H' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'I' => ['font' => ['name' => 'Consolas', 'size' => 11]],
            'J' => ['font' => ['name' => 'Consolas', 'size' => 11]],
        ];
    }
}
