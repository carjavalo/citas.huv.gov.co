<?php

/**
 * Script para corregir nombres de archivos con codificación incorrecta en Documentos/
 * Los archivos en disco tienen nombres con bytes corruptos pero la BD los tiene en UTF-8.
 * Compara usando solo caracteres ASCII para encontrar coincidencias y renombrar.
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

// Función para extraer solo caracteres ASCII de un string
function asciiOnly($str) {
    return preg_replace('/[^\x20-\x7E]/', '', $str);
}

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
$dirCache = []; // Cache de archivos por directorio

foreach ($dbPaths as $dbPath) {
    $fullPath = public_path($dbPath);
    
    // Si el archivo ya existe con el nombre correcto, skip
    if (file_exists($fullPath)) {
        $alreadyOk++;
        continue;
    }
    
    $dir = dirname($fullPath);
    $expectedBasename = basename($dbPath);
    $expectedAscii = asciiOnly($expectedBasename);
    
    if (!is_dir($dir)) {
        $notFound++;
        continue;
    }
    
    // Cache de archivos del directorio
    if (!isset($dirCache[$dir])) {
        $files = @scandir($dir);
        if ($files === false) {
            $notFound++;
            continue;
        }
        $dirCache[$dir] = array_filter($files, function($f) { return $f !== '.' && $f !== '..'; });
    }
    
    $matched = false;
    foreach ($dirCache[$dir] as $key => $file) {
        $fileAscii = asciiOnly($file);
        
        // Comparar partes ASCII — si coinciden, es el mismo archivo con encoding corrupto
        if ($fileAscii === $expectedAscii && $file !== $expectedBasename) {
            $oldPath = $dir . '/' . $file;
            $newPath = $dir . '/' . $expectedBasename;
            
            if (@rename($oldPath, $newPath)) {
                $fixed++;
                // Actualizar cache
                unset($dirCache[$dir][$key]);
                $dirCache[$dir][] = $expectedBasename;
                
                if ($fixed <= 30) {
                    echo "  Renombrado: $file\n";
                    echo "         => $expectedBasename\n";
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
echo "Archivos no encontrados en disco: $notFound\n";
echo "Errores: $errors\n";
