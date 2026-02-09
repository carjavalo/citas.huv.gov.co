<?php

/**
 * Script definitivo: Diagnóstico + Corrección de archivos con encoding corrupto
 * 
 * Estrategia: En vez de ir de BD→disco (lento con 100K+ rutas sin directorio),
 * va de disco→BD: escanea los archivos físicos y busca coincidencias.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$baseDir = public_path('Documentos');

if (!is_dir($baseDir)) {
    echo "ERROR: No se encontró la carpeta Documentos en: $baseDir\n";
    exit(1);
}

echo "=== Diagnóstico y Corrección de Encoding en Documentos/ ===\n\n";

// =============================================
// PASO 1: Escanear todos los archivos en disco
// =============================================
echo "[1/4] Escaneando archivos en disco...\n";
$diskFiles = [];     // fullPath => basename
$diskDirs = [];      // dir => [basename => fullPath]
$totalDiskFiles = 0;
$totalDiskDirs = 0;
$nonAsciiFiles = 0;

$rii = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($rii as $file) {
    if ($file->isDir()) {
        $totalDiskDirs++;
    } else {
        $totalDiskFiles++;
        $fullPath = $file->getPathname();
        $dir = dirname($fullPath);
        $basename = basename($fullPath);
        
        if (!isset($diskDirs[$dir])) {
            $diskDirs[$dir] = [];
        }
        $diskDirs[$dir][$basename] = $fullPath;
        
        // Detectar archivos con bytes no-UTF8 válidos en el nombre
        if (preg_match('/[\x80-\xFF]/', $basename) && !mb_check_encoding($basename, 'UTF-8')) {
            $nonAsciiFiles++;
        }
    }
}

echo "  Archivos en disco: $totalDiskFiles\n";
echo "  Directorios: $totalDiskDirs\n";
echo "  Archivos con encoding potencialmente corrupto: $nonAsciiFiles\n\n";

// =============================================
// PASO 2: Cargar rutas de la BD
// =============================================
echo "[2/4] Cargando rutas de la BD...\n";
$columns = ['pachis', 'pacordmed', 'pacauto', 'pacdocid', 'certfdo_cita', 'soporte_patologia'];

// Agrupar rutas de BD por directorio
$dbByDir = [];  // dir => [basename => [column, id]]
$totalDbPaths = 0;
$alreadyOk = 0;

foreach ($columns as $col) {
    $rows = DB::table('solicitudes')
        ->whereNotNull($col)
        ->where($col, '!=', '')
        ->select('id', $col)
        ->get();
    
    foreach ($rows as $row) {
        $totalDbPaths++;
        $ruta = $row->$col;
        $fullPath = public_path($ruta);
        
        // Si ya existe, contar y skip
        if (file_exists($fullPath)) {
            $alreadyOk++;
            continue;
        }
        
        $dir = dirname($fullPath);
        $basename = basename($ruta);
        
        if (!isset($dbByDir[$dir])) {
            $dbByDir[$dir] = [];
        }
        $dbByDir[$dir][] = [
            'basename' => $basename,
            'column' => $col,
            'id' => $row->id,
            'ruta' => $ruta,
        ];
    }
}

$totalMissing = array_sum(array_map('count', $dbByDir));
echo "  Total rutas en BD: $totalDbPaths\n";
echo "  Ya existen correctamente: $alreadyOk\n";
echo "  Faltan por encontrar: $totalMissing\n\n";

// =============================================
// PASO 3: Intentar match y renombrar
// =============================================
echo "[3/4] Buscando coincidencias y renombrando...\n\n";

// Función: extraer solo caracteres ASCII
function asciiOnly($str) {
    return preg_replace('/[^\x20-\x7E]/', '', $str);
}

// Función: normalizar quitando extensión y comparando
function normalizeForMatch($str) {
    // Quitar non-ASCII, bajar a minúsculas
    $ascii = preg_replace('/[^\x20-\x7E]/', '', $str);
    return mb_strtolower($ascii);
}

$renamed = 0;
$dirNotExist = 0;
$noMatch = 0;
$errors = 0;
$sampleNotFound = [];
$sampleRenamed = [];

foreach ($dbByDir as $dir => $entries) {
    // ¿El directorio existe en disco?
    if (!isset($diskDirs[$dir]) && !is_dir($dir)) {
        $dirNotExist += count($entries);
        continue;
    }
    
    // Obtener archivos reales del directorio
    if (isset($diskDirs[$dir])) {
        $realFiles = $diskDirs[$dir]; // basename => fullPath
    } else {
        // El directorio existe pero no estaba en nuestro cache (raro)
        $scanned = @scandir($dir);
        if (!$scanned) {
            $dirNotExist += count($entries);
            continue;
        }
        $realFiles = [];
        foreach (array_diff($scanned, ['.', '..']) as $f) {
            $realFiles[$f] = $dir . '/' . $f;
        }
    }
    
    // Pre-calcular ASCII normalizados de archivos en disco
    $realNormalized = [];
    foreach ($realFiles as $basename => $fullPath) {
        $norm = normalizeForMatch($basename);
        if (!isset($realNormalized[$norm])) {
            $realNormalized[$norm] = [];
        }
        $realNormalized[$norm][] = $basename;
    }
    
    foreach ($entries as $entry) {
        $expectedBasename = $entry['basename'];
        $expectedNorm = normalizeForMatch($expectedBasename);
        
        // Estrategia 1: Match exacto por ASCII normalizado
        if (isset($realNormalized[$expectedNorm])) {
            $candidates = $realNormalized[$expectedNorm];
            // Tomar el primer candidato que NO sea idéntico al esperado
            $matchedDiskFile = null;
            foreach ($candidates as $c) {
                if ($c !== $expectedBasename) {
                    $matchedDiskFile = $c;
                    break;
                }
            }
            
            if ($matchedDiskFile) {
                $oldPath = $dir . '/' . $matchedDiskFile;
                $newPath = $dir . '/' . $expectedBasename;
                
                if (!file_exists($newPath) && @rename($oldPath, $newPath)) {
                    $renamed++;
                    // Actualizar caches
                    unset($realFiles[$matchedDiskFile]);
                    $realFiles[$expectedBasename] = $newPath;
                    
                    if ($renamed <= 20) {
                        $sampleRenamed[] = "  [$matchedDiskFile] => [$expectedBasename]";
                    }
                    continue;
                } else {
                    $errors++;
                    continue;
                }
            }
        }
        
        // Estrategia 2: Match por similar_text (>80% similaridad)
        $bestMatch = null;
        $bestPercent = 0;
        foreach ($realFiles as $diskBasename => $diskFullPath) {
            similar_text($expectedBasename, $diskBasename, $percent);
            if ($percent > $bestPercent) {
                $bestPercent = $percent;
                $bestMatch = $diskBasename;
            }
        }
        
        if ($bestMatch && $bestPercent >= 80) {
            $oldPath = $dir . '/' . $bestMatch;
            $newPath = $dir . '/' . $expectedBasename;
            
            if (!file_exists($newPath) && @rename($oldPath, $newPath)) {
                $renamed++;
                unset($realFiles[$bestMatch]);
                $realFiles[$expectedBasename] = $newPath;
                
                if ($renamed <= 20) {
                    $sampleRenamed[] = "  [{$bestMatch}] => [{$expectedBasename}] ({$bestPercent}%)";
                }
                continue;
            } else {
                $errors++;
                continue;
            }
        }
        
        // No match
        $noMatch++;
        if (count($sampleNotFound) < 10) {
            $dirShort = str_replace(public_path() . '/', '', $dir);
            $sampleNotFound[] = [
                'dir' => $dirShort,
                'expected' => $expectedBasename,
                'expected_hex' => bin2hex($expectedBasename),
                'disk_files' => array_map(function($f) {
                    return $f . ' [hex:' . bin2hex($f) . ']';
                }, array_keys($realFiles)),
            ];
        }
    }
}

// =============================================
// PASO 4: Resultados
// =============================================
echo "[4/4] Resultados\n\n";
echo "=== RESUMEN ===\n";
echo "Total rutas en BD:           $totalDbPaths\n";
echo "Ya correctos:                $alreadyOk\n";
echo "Renombrados exitosamente:    $renamed\n";
echo "Dir no existe (sin archivo): $dirNotExist\n";
echo "Dir existe pero sin match:   $noMatch\n";
echo "Errores al renombrar:        $errors\n\n";

if (!empty($sampleRenamed)) {
    echo "=== Archivos renombrados (muestra) ===\n";
    foreach ($sampleRenamed as $s) {
        echo "$s\n";
    }
    echo "\n";
}

if (!empty($sampleNotFound)) {
    echo "=== Archivos sin match en directorio existente (muestra para debug) ===\n";
    foreach ($sampleNotFound as $s) {
        echo "  Dir: {$s['dir']}\n";
        echo "  Esperado: {$s['expected']}\n";
        echo "    hex: {$s['expected_hex']}\n";
        echo "  Archivos en disco:\n";
        foreach ($s['disk_files'] as $df) {
            echo "    - $df\n";
        }
        echo "\n";
    }
}

echo "Total archivos en disco: $totalDiskFiles\n";
echo "Archivos con encoding corrupto detectados: $nonAsciiFiles\n";
echo "\nProceso completado.\n";
