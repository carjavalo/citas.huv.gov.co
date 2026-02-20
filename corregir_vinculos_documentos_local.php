<?php
// Script para corregir la vinculación de documentos en solicitudes desde el id 140112
// Ejecutar: php artisan correccion:vinculos-solicitudes

use App\Models\solicitudes;

// Bootstrap de Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Inicializar el framework
$kernel->bootstrap();

$solicitudes = solicitudes::where('id', '>=', 140112)->get();

foreach ($solicitudes as $sol) {
    $userId = $sol->pacid;
    $solId = $sol->id;
    $rutaBase = "Documentos/usuario{$userId}/solicitud_{$solId}";

    $historia = glob(__DIR__ . "/public/{$rutaBase}/HC_*");
    $ordenMedica = glob(__DIR__ . "/public/{$rutaBase}/OM_*");
    $docId = glob(__DIR__ . "/public/{$rutaBase}/DI_*");
    $autorizacion = glob(__DIR__ . "/public/{$rutaBase}/AU_*");
    $soportePatologia = glob(__DIR__ . "/public/{$rutaBase}/SP_*");
    $codigoAutorizacion = glob(__DIR__ . "/public/{$rutaBase}/CAU_*");

    $update = [];
    if ($historia && isset($historia[0])) $update['pachis'] = $rutaBase . '/' . basename($historia[0]);
    if ($ordenMedica && isset($ordenMedica[0])) $update['pacordmed'] = $rutaBase . '/' . basename($ordenMedica[0]);
    if ($docId && isset($docId[0])) $update['pacdocid'] = $rutaBase . '/' . basename($docId[0]);
    if ($autorizacion && isset($autorizacion[0])) $update['pacauto'] = $rutaBase . '/' . basename($autorizacion[0]);
    if ($soportePatologia && isset($soportePatologia[0])) $update['soporte_patologia'] = $rutaBase . '/' . basename($soportePatologia[0]);
    if ($codigoAutorizacion && isset($codigoAutorizacion[0])) {
        $nombreArchivo = basename($codigoAutorizacion[0]);
        if (preg_match('/CAU_(.+)\./', $nombreArchivo, $matches)) {
            $update['codigo_autorizacion'] = $matches[1];
        } else {
            $update['codigo_autorizacion'] = $nombreArchivo;
        }
    }

    if (!empty($update)) {
        $sol->update($update);
        echo "Solicitud #{$solId} corregida.\n";
    }
}

echo "Corrección finalizada.\n";
