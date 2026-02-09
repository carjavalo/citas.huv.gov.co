<?php

/**
 * Script para corregir nombres de archivos con codificación incorrecta en Documentos/
 * Los archivos en disco tienen nombres en Latin-1/CP1252 pero la BD los tiene en UTF-8.
 * Este script renombra los archivos en disco para que coincidan con la BD.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$basePath = public_path('Documentos');

if (!is_dir($basePath)) {
    echo "ERROR: No se encontró la carpeta Documentos en: $basePath\n";
    exit(1);
}

echo "=== Corrección de nombres de archivos con encoding incorrecto ===\n\n";

// Obtener todas las rutas de archivos de la BD
$columns = ['pachis', 'pacordmed', 'pacauto', 'pacdocid', 'certfdo_cita', 'soporte_patologia'];

$dbPaths = [];
foreach ($columns as $col) {
    $paths = DB::table('solicitudes')
        ->whereNotNull($col)
        ->where($col, '!=', '')
        ->pluck($col)
        ->toArray();
    foreach ($paths as $p) {
        $dbPaths[] = $p;
    }
}

echo "Total rutas en BD: " . count($dbPaths) . "\n";

$fixed = 0;
$notFound = 0;
$alreadyOk = 0;
$errors = 0;

foreach ($dbPaths as $dbPath) {
    $fullPath = public_path($dbPath);
    
    // Si el archivo ya existe con el nombre correcto, skip
    if (file_exists($fullPath)) {
        $alreadyOk++;
        continue;
    }
    
    // El archivo no existe con el nombre UTF-8. Buscar en el directorio
    $dir = dirname($fullPath);
    $expectedBasename = basename($dbPath);
    
    if (!is_dir($dir)) {
        $notFound++;
        continue;
    }
    
    // Listar archivos en el directorio y buscar coincidencia
    $files = @scandir($dir);
    if ($files === false) {
        $notFound++;
        continue;
    }
    
    $matched = false;
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        // Intentar convertir el nombre del archivo de Latin-1 a UTF-8
        $converted = @iconv('CP1252', 'UTF-8//IGNORE', $file);
        if ($converted === false) {
            $converted = @mb_convert_encoding($file, 'UTF-8', 'ISO-8859-1');
        }
        
        if ($converted === $expectedBasename) {
            // Encontramos el archivo con nombre corrupto, renombrarlo
            $oldPath = $dir . DIRECTORY_SEPARATOR . $file;
            $newPath = $dir . DIRECTORY_SEPARATOR . $expectedBasename;
            
            if (@rename($oldPath, $newPath)) {
                $fixed++;
                if ($fixed <= 20) {
                    echo "  Renombrado: $file => $expectedBasename\n";
                }
                $matched = true;
                break;
            } else {
                $errors++;
                if ($errors <= 10) {
                    echo "  ERROR renombrando: $oldPath\n";
                }
            }
        }
    }
    
    if (!$matched) {
        $notFound++;
    }
}

echo "\n=== Resultado ===\n";
echo "Archivos ya correctos (UTF-8): $alreadyOk\n";
echo "Archivos renombrados: $fixed\n";
echo "Archivos no encontrados: $notFound\n";
echo "Errores: $errors\n";
