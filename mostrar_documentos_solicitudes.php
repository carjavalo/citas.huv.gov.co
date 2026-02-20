<?php
// Script para mostrar los campos de documentos de solicitudes desde el id 140112
// Ejecutar: php mostrar_documentos_solicitudes.php

use App\Models\solicitudes;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$solicitudes = solicitudes::where('id','>=',140112)
    ->get(['id','pachis','pacordmed','pacdocid','pacauto','soporte_patologia','codigo_autorizacion']);

foreach ($solicitudes as $sol) {
    echo "ID: {$sol->id}\n";
    echo "  Historia:           {$sol->pachis}\n";
    echo "  Orden Médica:       {$sol->pacordmed}\n";
    echo "  Doc. Identidad:     {$sol->pacdocid}\n";
    echo "  Autorización EPS:   {$sol->pacauto}\n";
    echo "  Soporte Patología:  {$sol->soporte_patologia}\n";
    echo "  Código Autorización:{$sol->codigo_autorizacion}\n";
    echo str_repeat('-',40)."\n";
}
