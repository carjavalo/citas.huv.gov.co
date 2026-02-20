<?php
// Script para vincular documentos antiguos a solicitudes existentes
// Ejecutar desde terminal: php vincular_documentos_solicitudes.php

use Illuminate\Database\Capsule\Manager as DB;

require __DIR__ . '/vendor/autoload.php';

// Configuración de conexión (ajustar si es necesario)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new DB();
$db->addConnection([
    'driver'    => getenv('DB_CONNECTION'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_DATABASE'),
    'username'  => getenv('DB_USERNAME'),
    'password'  => getenv('DB_PASSWORD'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$db->setAsGlobal();
$db->bootEloquent();

// --- Lógica de vinculación ---

$solicitudes = DB::table('solicitudes')->get();

foreach ($solicitudes as $sol) {
    $userId = $sol->pacid;
    $solId = $sol->id;
    $rutaBase = "Documentos/usuario{$userId}/solicitud_{$solId}";

    // Vincular historia clínica
    $historia = glob(__DIR__ . "/public/{$rutaBase}/HC_*");
    $ordenMedica = glob(__DIR__ . "/public/{$rutaBase}/OM_*");
    $docId = glob(__DIR__ . "/public/{$rutaBase}/DI_*");
    $autorizacion = glob(__DIR__ . "/public/{$rutaBase}/AU_*");
    $soportePatologia = glob(__DIR__ . "/public/{$rutaBase}/SP_*");

    $update = [];
    if ($historia && isset($historia[0])) $update['pachis'] = $rutaBase . '/' . basename($historia[0]);
    if ($ordenMedica && isset($ordenMedica[0])) $update['pacordmed'] = $rutaBase . '/' . basename($ordenMedica[0]);
    if ($docId && isset($docId[0])) $update['pacdocid'] = $rutaBase . '/' . basename($docId[0]);
    if ($autorizacion && isset($autorizacion[0])) $update['pacauto'] = $rutaBase . '/' . basename($autorizacion[0]);
    if ($soportePatologia && isset($soportePatologia[0])) $update['soporte_patologia'] = $rutaBase . '/' . basename($soportePatologia[0]);

    if (!empty($update)) {
        DB::table('solicitudes')->where('id', $solId)->update($update);
        echo "Solicitud #{$solId} actualizada.\n";
    }
}

echo "Vinculación finalizada.\n";
