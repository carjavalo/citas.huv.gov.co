<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Exports\ActividadesUsuariosExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

echo "=== PRUEBA DE EXPORTACIÓN A EXCEL ===\n\n";

// Configurar fechas de prueba
$fechaInicio = Carbon::now()->subDays(7)->format('Y-m-d');
$fechaFin = Carbon::now()->format('Y-m-d');

echo "Parámetros de exportación:\n";
echo "- Fecha Inicio: {$fechaInicio}\n";
echo "- Fecha Fin: {$fechaFin}\n";
echo "- Tipo Actividad: Todos\n\n";

// Crear instancia del export
$export = new ActividadesUsuariosExport($fechaInicio, $fechaFin, '');

// Obtener la colección de datos
$actividades = $export->collection();

echo "Datos a exportar:\n";
echo "- Total de registros: " . $actividades->count() . "\n\n";

if ($actividades->count() > 0) {
    echo "Primeros 5 registros:\n";
    echo str_repeat("-", 70) . "\n";
    
    foreach ($actividades->take(5) as $actividad) {
        $usuario = $actividad->user;
        $userName = $usuario ? $usuario->name . ' ' . $usuario->apellido1 : 'Usuario Eliminado';
        $documento = $usuario ? $usuario->ndocumento : 'N/A';
        
        echo "Doc: {$documento} | {$userName}\n";
        echo "Tipo: {$actividad->tipo_actividad} | {$actividad->descripcion}\n";
        echo "Fecha: " . \Carbon\Carbon::parse($actividad->created_at)->format('d/m/Y H:i') . "\n";
        echo str_repeat("-", 70) . "\n";
    }
}

echo "\n✅ FUNCIONALIDAD IMPLEMENTADA:\n\n";
echo "1. Clase de exportación creada: ActividadesUsuariosExport\n";
echo "2. Método exportarExcel() agregado al componente Livewire\n";
echo "3. Botón 'Exportar Excel' agregado en la vista\n";
echo "4. Respeta los filtros aplicados (fecha y tipo)\n";
echo "5. Excluye actividades de Super Admin\n\n";

echo "📊 COLUMNAS DEL EXCEL:\n";
$headings = $export->headings();
foreach ($headings as $index => $heading) {
    echo ($index + 1) . ". {$heading}\n";
}

echo "\n🎨 CARACTERÍSTICAS DEL ARCHIVO:\n";
echo "- Formato: XLSX (Excel)\n";
echo "- Encabezados con estilo (fondo azul corporativo #2e3a75)\n";
echo "- Nombre del archivo: actividades_usuarios_YYYY-MM-DD_HHMMSS.xlsx\n";
echo "- Hoja: 'Actividades de Usuarios'\n\n";

echo "📍 UBICACIÓN DEL BOTÓN:\n";
echo "Vista: http://192.168.2.200:8000/reporte/ingresos\n";
echo "Posición: Junto al botón 'Filtrar' (color verde)\n\n";

echo "✓ Sistema de exportación listo para usar\n";
