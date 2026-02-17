<?php
/**
 * Script de diagnóstico y corrección de documentos de solicitudes.
 * 
 * Este script:
 * 1. Identifica solicitudes con rutas de documentos inconsistentes (path no coincide con pacid o solnum)
 * 2. Corrige las rutas en la BD si los archivos existen en la ubicación correcta
 * 3. Genera un reporte de solicitudes con archivos faltantes
 * 
 * USO: php diagnostico_documentos.php [--fix] [--verbose]
 *   --fix     : Aplicar correcciones automáticamente (sin este flag, solo muestra el diagnóstico)
 *   --verbose : Mostrar todas las solicitudes, no solo las problemáticas
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$fix = in_array('--fix', $argv ?? []);
$verbose = in_array('--verbose', $argv ?? []);

$docFields = ['pachis', 'pacordmed', 'pacdocid', 'pacauto', 'soporte_patologia'];

echo "================================================================\n";
echo "  DIAGNÓSTICO DE DOCUMENTOS DE SOLICITUDES\n";
echo "  Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "  Modo: " . ($fix ? "CORRECCIÓN ACTIVA" : "Solo diagnóstico (usar --fix para corregir)") . "\n";
echo "================================================================\n\n";

// 1. Buscar solnums duplicados
echo "--- 1. VERIFICANDO SOLNUMS DUPLICADOS ---\n";
$duplicados = DB::select("
    SELECT pacid, solnum, COUNT(*) as total, 
           GROUP_CONCAT(id ORDER BY id SEPARATOR ', ') as ids
    FROM solicitudes
    GROUP BY pacid, solnum
    HAVING COUNT(*) > 1
    ORDER BY total DESC
");

if (empty($duplicados)) {
    echo "✓ No hay solnums duplicados.\n\n";
} else {
    echo "✗ Se encontraron " . count($duplicados) . " grupos con solnums duplicados:\n";
    foreach ($duplicados as $d) {
        echo "  pacid=$d->pacid solnum=$d->solnum total=$d->total ids=[$d->ids]\n";
        
        if ($fix) {
            // Corregir: reasignar solnums únicos manteniendo el orden por ID
            $solicitudes = App\Models\solicitudes::where('pacid', $d->pacid)
                ->where('solnum', $d->solnum)
                ->orderBy('id', 'asc')
                ->get();
            
            foreach ($solicitudes as $index => $sol) {
                if ($index === 0) continue; // Mantener el primero
                
                // Calcular nuevo solnum
                $maxSolnum = App\Models\solicitudes::where('pacid', $d->pacid)->max('solnum');
                $nuevoSolnum = $maxSolnum + 1;
                
                // Calcular nuevas rutas
                $rutaBase = 'Documentos/usuario' . $d->pacid . '/solicitud_' . $nuevoSolnum;
                $rutaAnterior = 'Documentos/usuario' . $d->pacid . '/solicitud_' . $d->solnum;
                
                $updates = ['solnum' => $nuevoSolnum];
                
                foreach ($docFields as $field) {
                    if ($sol->$field) {
                        $fileName = basename($sol->$field);
                        $updates[$field] = $rutaBase . '/' . $fileName;
                    }
                }
                
                // Mover archivos en disco si existen
                $oldDir = public_path($rutaAnterior);
                $newDir = public_path($rutaBase);
                
                if (is_dir($oldDir) && !is_dir($newDir)) {
                    // Solo mover si hay archivos que corresponden a esta solicitud
                    @mkdir($newDir, 0755, true);
                    foreach ($docFields as $field) {
                        if ($sol->$field) {
                            $oldFile = public_path($sol->$field);
                            $newFile = public_path($updates[$field]);
                            if (file_exists($oldFile) && !file_exists($newFile)) {
                                @copy($oldFile, $newFile);
                            }
                        }
                    }
                }
                
                // Actualizar BD
                App\Models\solicitudes::where('id', $sol->id)->update($updates);
                echo "  → CORREGIDO: ID:{$sol->id} solnum cambiado de {$d->solnum} a {$nuevoSolnum}\n";
            }
        }
    }
    echo "\n";
}

// 2. Verificar rutas inconsistentes (path no coincide con pacid o solnum)
echo "--- 2. VERIFICANDO RUTAS INCONSISTENTES ---\n";
$inconsistentes = 0;
$corregidas = 0;

$solicitudes = App\Models\solicitudes::whereNotNull('pachis')
    ->orderBy('id', 'desc')
    ->chunk(500, function ($sols) use ($docFields, $fix, &$inconsistentes, &$corregidas) {
        foreach ($sols as $sol) {
            $problemas = [];
            
            foreach ($docFields as $field) {
                if (empty($sol->$field)) continue;
                
                // Verificar que la ruta contiene el usuario correcto
                if (!str_contains($sol->$field, 'usuario' . $sol->pacid . '/')) {
                    $problemas[] = "$field: ruta apunta a otro usuario ({$sol->$field})";
                }
                
                // Verificar que la ruta contiene el solnum correcto
                if (!str_contains($sol->$field, 'solicitud_' . $sol->solnum . '/')) {
                    $problemas[] = "$field: ruta apunta a otro solnum ({$sol->$field})";
                }
            }
            
            if (!empty($problemas)) {
                $inconsistentes++;
                echo "  ✗ ID:{$sol->id} pacid:{$sol->pacid} solnum:{$sol->solnum}\n";
                foreach ($problemas as $p) {
                    echo "    - $p\n";
                }
                
                if ($fix) {
                    $rutaBase = 'Documentos/usuario' . $sol->pacid . '/solicitud_' . $sol->solnum;
                    $updates = [];
                    
                    foreach ($docFields as $field) {
                        if (empty($sol->$field)) continue;
                        $fileName = basename($sol->$field);
                        $rutaCorrecta = $rutaBase . '/' . $fileName;
                        
                        if ($sol->$field !== $rutaCorrecta) {
                            $updates[$field] = $rutaCorrecta;
                            
                            // Mover archivo si existe en la ruta incorrecta
                            $archivoIncorrecto = public_path($sol->$field);
                            $archivoCorrecto = public_path($rutaCorrecta);
                            
                            if (file_exists($archivoIncorrecto) && !file_exists($archivoCorrecto)) {
                                @mkdir(dirname($archivoCorrecto), 0755, true);
                                @copy($archivoIncorrecto, $archivoCorrecto);
                            }
                        }
                    }
                    
                    if (!empty($updates)) {
                        App\Models\solicitudes::where('id', $sol->id)->update($updates);
                        $corregidas++;
                        echo "    → CORREGIDO\n";
                    }
                }
            }
        }
    });

if ($inconsistentes === 0) {
    echo "✓ Todas las rutas son consistentes con pacid y solnum.\n\n";
} else {
    echo "Total inconsistentes: $inconsistentes" . ($fix ? ", Corregidas: $corregidas" : "") . "\n\n";
}

// 3. Verificar archivos faltantes en las últimas N solicitudes
echo "--- 3. VERIFICANDO ARCHIVOS EN DISCO (últimas 200 solicitudes) ---\n";
$sinArchivos = 0;
$conArchivos = 0;

$recientes = App\Models\solicitudes::whereNotNull('pachis')
    ->orderBy('id', 'desc')
    ->take(200)
    ->get();

foreach ($recientes as $sol) {
    $faltantes = [];
    $encontrados = [];
    
    foreach ($docFields as $field) {
        if (empty($sol->$field)) continue;
        
        $fullPath = public_path($sol->$field);
        if (file_exists($fullPath)) {
            $encontrados[] = $field;
        } else {
            $faltantes[] = $field;
        }
    }
    
    if (!empty($faltantes)) {
        $sinArchivos++;
        if ($verbose || count($encontrados) > 0) {
            echo "  ⚠ ID:{$sol->id} pacid:{$sol->pacid} solnum:{$sol->solnum} ";
            echo "encontrados=[" . implode(',', $encontrados) . "] ";
            echo "faltantes=[" . implode(',', $faltantes) . "]\n";
        }
    } else {
        $conArchivos++;
    }
}

echo "Con todos los archivos: $conArchivos | Con archivos faltantes: $sinArchivos (de 200 revisadas)\n\n";

// 4. Resumen
echo "================================================================\n";
echo "  RESUMEN\n";
echo "================================================================\n";
echo "Total solicitudes en BD: " . App\Models\solicitudes::count() . "\n";
echo "Duplicados de solnum: " . count($duplicados ?? []) . "\n";
echo "Rutas inconsistentes: $inconsistentes\n";
echo "Archivos faltantes (últimas 200): $sinArchivos\n";
echo "================================================================\n";

if (!$fix && ($inconsistentes > 0 || !empty($duplicados))) {
    echo "\nPara aplicar correcciones, ejecutar:\n";
    echo "  php diagnostico_documentos.php --fix\n";
}
