<?php

/**
 * Diagnóstico: ¿cuántos archivos hay en disco vs cuántos referencia la BD?
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$baseDir = public_path('Documentos');

// 1. Contar archivos físicos en disco (recursivo)
echo "=== Diagnóstico de archivos en Documentos/ ===\n\n";

$totalFiles = 0;
$totalDirs = 0;
$sampleFiles = [];

$rii = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($rii as $file) {
    if ($file->isDir()) {
        $totalDirs++;
    } else {
        $totalFiles++;
        if (count($sampleFiles) < 10) {
            $sampleFiles[] = str_replace($baseDir . '/', '', $file->getPathname());
        }
    }
}

echo "Archivos físicos en disco: $totalFiles\n";
echo "Directorios en disco: $totalDirs\n\n";

// 2. Contar rutas únicas en BD
$dbPaths = DB::table('solicitudes')
    ->whereNotNull('ruta_archivos')
    ->where('ruta_archivos', '!=', '')
    ->pluck('ruta_archivos');

$totalDbPaths = $dbPaths->count();
echo "Rutas en BD (solicitudes.ruta_archivos): $totalDbPaths\n\n";

// 3. Verificar cuántos "not found" tienen directorio que sí existe
$notFoundDirExists = 0;
$notFoundDirMissing = 0;
$sampleNotFoundDirExists = [];
$sampleNotFoundDirMissing = [];

foreach ($dbPaths as $ruta) {
    $fullPath = public_path($ruta);
    if (file_exists($fullPath)) {
        continue; // ya existe, skip
    }
    
    $dir = dirname($fullPath);
    if (is_dir($dir)) {
        $notFoundDirExists++;
        if (count($sampleNotFoundDirExists) < 5) {
            // Mostrar qué archivos HAY en ese directorio
            $filesInDir = @scandir($dir);
            $filesInDir = array_diff($filesInDir ?: [], ['.', '..']);
            $sampleNotFoundDirExists[] = [
                'expected' => basename($fullPath),
                'dir' => str_replace(public_path() . '/', '', $dir),
                'actual_files' => array_slice(array_values($filesInDir), 0, 5),
            ];
        }
    } else {
        $notFoundDirMissing++;
        if (count($sampleNotFoundDirMissing) < 5) {
            $sampleNotFoundDirMissing[] = str_replace(public_path() . '/', '', $dir);
        }
    }
}

echo "=== Archivos no encontrados ===\n";
echo "Directorio existe pero archivo no: $notFoundDirExists\n";
echo "Directorio NO existe: $notFoundDirMissing\n\n";

if (!empty($sampleNotFoundDirExists)) {
    echo "=== Muestra: dir existe, archivo no (posible encoding) ===\n";
    foreach ($sampleNotFoundDirExists as $s) {
        echo "  Dir: {$s['dir']}\n";
        echo "  Esperado (BD): {$s['expected']}\n";
        echo "  Archivos reales en dir:\n";
        foreach ($s['actual_files'] as $f) {
            echo "    - $f\n";
            // Mostrar bytes hex del nombre
            echo "      hex: " . bin2hex($f) . "\n";
        }
        echo "  Esperado hex: " . bin2hex($s['expected']) . "\n";
        echo "\n";
    }
}

if (!empty($sampleNotFoundDirMissing)) {
    echo "=== Muestra: directorio NO existe ===\n";
    foreach ($sampleNotFoundDirMissing as $d) {
        echo "  $d\n";
    }
    echo "\n";
}

// 4. Muestra de archivos físicos
echo "=== Muestra de archivos físicos en disco ===\n";
foreach ($sampleFiles as $f) {
    echo "  $f\n";
}

echo "\nDiagnóstico completado.\n";
