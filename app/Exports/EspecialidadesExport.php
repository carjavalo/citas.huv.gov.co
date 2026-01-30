<?php

namespace App\Exports;

use App\Models\servicios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class EspecialidadesExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithTitle, WithEvents, WithCharts
{
    private $fechaDesde;
    private $fechaHasta;
    private $totalAgendado = 0;
    private $totalEspera = 0;
    private $totalPendiente = 0;
    private $totalRechazado = 0;

    public function __construct($fechaDesde, $fechaHasta)
    {
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
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
                $query->where('pservicios.sede_id', $user->sede_id);
            }
            if ($user->pservicio_id) {
                $query->where('servicios.id_pservicios', $user->pservicio_id);
            }
        }
        
        return $query;
    }

    public function collection()
    {
        $query = servicios::select(
                'servicios.servcod',
                'servicios.servnomb',
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Agendado" THEN 1 ELSE 0 END), 0) as agendadas'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Espera" THEN 1 ELSE 0 END), 0) as en_espera'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Pendiente" THEN 1 ELSE 0 END), 0) as pendientes'),
                DB::raw('COALESCE(SUM(CASE WHEN solicitudes.estado = "Rechazado" THEN 1 ELSE 0 END), 0) as rechazadas')
            )
            ->leftJoin('solicitudes', function($join) {
                $join->on('servicios.servcod', '=', 'solicitudes.espec')
                     ->whereDate('solicitudes.created_at', '>=', $this->fechaDesde)
                     ->whereDate('solicitudes.created_at', '<=', $this->fechaHasta);
            })
            ->join('pservicios', 'servicios.id_pservicios', '=', 'pservicios.id')
            ->where('servicios.estado', 1);
        
        // Aplicar filtros de visibilidad según rol
        $this->aplicarFiltrosVisibilidad($query);
        
        $data = $query->groupBy('servicios.servcod', 'servicios.servnomb')
            ->orderBy('servicios.servnomb')
            ->get();

        // Calcular totales
        $this->totalAgendado = $data->sum('agendadas');
        $this->totalEspera = $data->sum('en_espera');
        $this->totalPendiente = $data->sum('pendientes');
        $this->totalRechazado = $data->sum('rechazadas');

        // Agregar columna de total por fila
        $result = $data->map(function($item) {
            return [
                'codigo' => $item->servcod,
                'especialidad' => $item->servnomb,
                'agendadas' => $item->agendadas,
                'en_espera' => $item->en_espera,
                'pendientes' => $item->pendientes,
                'rechazadas' => $item->rechazadas,
                'total' => $item->agendadas + $item->en_espera + $item->pendientes + $item->rechazadas,
            ];
        });

        return $result;
    }

    public function headings(): array
    {
        return [
            'CÓDIGO',
            'ESPECIALIDAD',
            'AGENDADAS',
            'EN ESPERA',
            'PENDIENTES',
            'RECHAZADAS',
            'TOTAL',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 45,
            'C' => 14,
            'D' => 14,
            'E' => 14,
            'F' => 14,
            'G' => 12,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2c4370'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Reporte Especialidades';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                // Agregar fila de totales
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'TOTALES');
                $sheet->setCellValue('C' . $totalRow, $this->totalAgendado);
                $sheet->setCellValue('D' . $totalRow, $this->totalEspera);
                $sheet->setCellValue('E' . $totalRow, $this->totalPendiente);
                $sheet->setCellValue('F' . $totalRow, $this->totalRechazado);
                $sheet->setCellValue('G' . $totalRow, $this->totalAgendado + $this->totalEspera + $this->totalPendiente + $this->totalRechazado);

                // Estilo fila de totales
                $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E5E7EB'],
                    ],
                ]);

                // Bordes para toda la tabla
                $sheet->getStyle('A1:G' . $totalRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Centrar columnas numéricas
                $sheet->getStyle('C2:G' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Insertar filas para título, período y resumen de distribución
                $sheet->insertNewRowBefore(1, 10);
                
                // Título principal
                $sheet->setCellValue('A1', 'REPORTE DE ESPECIALIDADES');
                $sheet->setCellValue('A2', 'Período: ' . \Carbon\Carbon::parse($this->fechaDesde)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($this->fechaHasta)->format('d/m/Y'));
                
                // Sección de Distribución por Estado
                $sheet->setCellValue('A4', 'DISTRIBUCIÓN POR ESTADO');
                $sheet->setCellValue('A5', 'Estado');
                $sheet->setCellValue('B5', 'Cantidad');
                $sheet->setCellValue('C5', 'Porcentaje');
                
                $total = $this->totalAgendado + $this->totalEspera + $this->totalPendiente + $this->totalRechazado;
                $porcAgendado = $total > 0 ? round(($this->totalAgendado / $total) * 100, 1) : 0;
                $porcEspera = $total > 0 ? round(($this->totalEspera / $total) * 100, 1) : 0;
                $porcPendiente = $total > 0 ? round(($this->totalPendiente / $total) * 100, 1) : 0;
                $porcRechazado = $total > 0 ? round(($this->totalRechazado / $total) * 100, 1) : 0;
                
                $sheet->setCellValue('A6', 'Agendadas');
                $sheet->setCellValue('B6', $this->totalAgendado);
                $sheet->setCellValue('C6', $porcAgendado . '%');
                
                $sheet->setCellValue('A7', 'En Espera');
                $sheet->setCellValue('B7', $this->totalEspera);
                $sheet->setCellValue('C7', $porcEspera . '%');
                
                $sheet->setCellValue('A8', 'Pendientes');
                $sheet->setCellValue('B8', $this->totalPendiente);
                $sheet->setCellValue('C8', $porcPendiente . '%');
                
                $sheet->setCellValue('A9', 'Rechazadas');
                $sheet->setCellValue('B9', $this->totalRechazado);
                $sheet->setCellValue('C9', $porcRechazado . '%');
                
                // Estilo del título principal
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2c4370']],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
                ]);
                
                // Estilo sección distribución
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '2c4370']],
                ]);
                $sheet->getStyle('A5:C5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2c4370'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Colores por estado
                $sheet->getStyle('A6')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
                ]);
                $sheet->getStyle('A7')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                ]);
                $sheet->getStyle('A8')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                ]);
                $sheet->getStyle('A9')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
                ]);
                
                // Centrar valores de distribución
                $sheet->getStyle('B5:C9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Bordes para tabla de distribución
                $sheet->getStyle('A5:C9')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Merge cells para títulos
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A4:C4');
            },
        ];
    }

    public function charts()
    {
        // Etiquetas de categorías (Estados)
        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Reporte Especialidades!$A$6:$A$9', null, 4),
        ];

        // Valores de datos
        $values = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Reporte Especialidades!$B$6:$B$9', null, 4),
        ];

        // Crear serie de datos tipo PIE
        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values) - 1),
            [],
            $categories,
            $values
        );

        // Área del gráfico
        $plotArea = new PlotArea(null, [$series]);

        // Leyenda
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);

        // Título del gráfico
        $title = new Title('Distribución por Estado');

        // Crear el gráfico
        $chart = new Chart(
            'chart1',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        // Posición del gráfico (columnas D-G, filas 4-9)
        $chart->setTopLeftPosition('D4');
        $chart->setBottomRightPosition('G10');

        return $chart;
    }
}
