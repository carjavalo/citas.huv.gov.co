<?php
/**
 * Script de diagnóstico profundo de solicitudes históricas.
 * Analiza la correspondencia entre rutas de documentos en BD y archivos en disco.
 * Identifica solicitudes con rutas basadas en solnum (formato antiguo).
 */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\solicitudes;
use Illuminate\Support\Facades\DB;

$basePath = __DIR__ . '/public/';

echo "=== ANÁLISIS COMPLETO DE SOLICITUDES ===\n\n";

// 1. Contar total de solicitudes
$total = solicitudes::count();
echo "Total de solicitudes en BD: {$total}\n";

// 2. Identificar solicitudes con formato antiguo (solicitud_{solnum}) vs nuevo (solicitud_{id})
$conFormatoAntiguo = 0;
$conFormatoNuevo = 0;
$sinDocumentos = 0;
$conPendiente = 0;
$archivosNoExisten = 0;
$docsIguales = 0;

// Analizar las solicitudes desde la más reciente que podría tener el formato antiguo
// (antes del fix, que fue en el commit reciente)
$solicitudes = solicitudes::orderBy('id', 'desc')->get();

$problematicas = [];
$formatoAntiguo = [];

foreach ($solicitudes as $s) {
    $expectedNewPath = "solicitud_{$s->id}";
    $expectedOldPath = "solicitud_{$s->solnum}";
    
    // Si pachis es null o 'pendiente', tiene problema
    if (!$s->pachis || $s->pachis === 'pendiente') {
        $conPendiente++;
        continue;
    }
    
    // Verificar si usa formato nuevo (solicitud_{id}) o antiguo (solicitud_{solnum})
    if (strpos($s->pachis, $expectedNewPath) !== false) {
        $conFormatoNuevo++;
    } elseif (strpos($s->pachis, $expectedOldPath) !== false || strpos($s->pachis, 'solicitud_') !== false) {
        $conFormatoAntiguo++;
        $formatoAntiguo[] = $s;
    }
    
    // Verificar si todos los docs son iguales (problema de sobreescritura)
    if ($s->pachis === $s->pacordmed && $s->pachis === $s->pacdocid) {
        $docsIguales++;
    }
    
    // Verificar si archivos existen en disco
    $docFields = ['pachis', 'pacordmed', 'pacdocid'];
    foreach ($docFields as $f) {
        if ($s->$f && $s->$f !== 'pendiente') {
            if (!file_exists($basePath . $s->$f)) {
                $archivosNoExisten++;
            }
        }
    }
}

echo "Con formato NUEVO (solicitud_{id}): {$conFormatoNuevo}\n";
echo "Con formato ANTIGUO (solicitud_{solnum}): {$conFormatoAntiguo}\n";
echo "Con documentos 'pendiente': {$conPendiente}\n";
echo "Con todos los docs iguales: {$docsIguales}\n";
echo "Archivos que no existen en disco: {$archivosNoExisten}\n\n";

// 3. Analizar las solicitudes con formato antiguo que tienen colisión
echo "=== SOLICITUDES CON POSIBLE COLISIÓN DE CARPETA ===\n";
echo "(Múltiples solicitudes del mismo usuario usando la misma carpeta solicitud_{solnum})\n\n";

// Agrupar por usuario + ruta base
$porRuta = [];
foreach ($formatoAntiguo as $s) {
    // Extraer ruta base (ej: Documentos/usuario58316/solicitud_7)
    if (preg_match('#(Documentos/usuario\d+/solicitud_\d+)#', $s->pachis, $matches)) {
        $rutaBase = $matches[1];
        $porRuta[$rutaBase][] = $s;
    }
}

$colisiones = 0;
foreach ($porRuta as $ruta => $solicitudes_ruta) {
    if (count($solicitudes_ruta) > 1) {
        $colisiones++;
        echo "COLISIÓN en ruta: {$ruta}\n";
        foreach ($solicitudes_ruta as $s) {
            echo "  ID:{$s->id} solnum:{$s->solnum} espec:{$s->espec} estado:{$s->estado} created:{$s->created_at}\n";
        }
        echo "\n";
    }
}
echo "Total colisiones de carpeta: {$colisiones}\n\n";

// 4. Mostrar últimas 10 solicitudes del formato antiguo con detalle
echo "=== ÚLTIMAS 10 SOLICITUDES FORMATO ANTIGUO ===\n";
$ultimasAntiguas = array_slice($formatoAntiguo, 0, 10);
foreach ($ultimasAntiguas as $s) {
    echo "ID:{$s->id} pacid:{$s->pacid} solnum:{$s->solnum} estado:{$s->estado}\n";
    echo "  pachis:   {$s->pachis}\n";
    echo "  pacordmed: {$s->pacordmed}\n";
    echo "  pacdocid:  {$s->pacdocid}\n";
    echo "  pacauto:   " . ($s->pacauto ?? 'NULL') . "\n";
    
    // Verificar existencia de archivos
    $docFields = ['pachis', 'pacordmed', 'pacdocid', 'pacauto'];
    foreach ($docFields as $f) {
        if ($s->$f && $s->$f !== 'pendiente') {
            $exists = file_exists($basePath . $s->$f);
            if (!$exists) {
                echo "  ⚠ {$f} NO EXISTE EN DISCO\n";
            }
        }
    }
    echo "---\n";
}

// 5. Resumen de corrección necesaria
echo "\n=== RESUMEN DE ACCIONES NECESARIAS ===\n";
echo "1. Solicitudes con formato antiguo que necesitan migrar rutas: {$conFormatoAntiguo}\n";
echo "2. Colisiones de carpeta (múltiples solicitudes en misma carpeta): {$colisiones}\n";
echo "3. Solicitudes con documentos idénticos en todos los campos: {$docsIguales}\n";
echo "4. Archivos referenciados que no existen en disco: {$archivosNoExisten}\n";
