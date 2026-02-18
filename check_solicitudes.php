<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\solicitudes;

echo "=== ULTIMAS 30 SOLICITUDES ===\n";
$solicitudes = solicitudes::orderBy('id','desc')->take(30)->get();
foreach($solicitudes as $s) {
    echo "ID:{$s->id} | pacid:{$s->pacid} | solnum:{$s->solnum} | estado:{$s->estado}\n";
    echo "  pachis: " . ($s->pachis ?? 'NULL') . "\n";
    echo "  pacordmed: " . ($s->pacordmed ?? 'NULL') . "\n";
    echo "  pacauto: " . ($s->pacauto ?? 'NULL') . "\n";
    echo "  pacdocid: " . ($s->pacdocid ?? 'NULL') . "\n";
    echo "  soporte_patologia: " . ($s->soporte_patologia ?? 'NULL') . "\n";
    echo "  created_at: {$s->created_at}\n";
    echo "---\n";
}

echo "\n=== SOLICITUDES CON solnum=0 O NULL ===\n";
$sinConsecutivo = solicitudes::where('solnum', 0)->orWhereNull('solnum')->count();
echo "Total sin consecutivo: {$sinConsecutivo}\n";

echo "\n=== SOLICITUDES CON DOCUMENTOS 'pendiente' ===\n";
$pendientes = solicitudes::where('pachis', 'pendiente')
    ->orWhere('pacordmed', 'pendiente')
    ->orWhere('pacdocid', 'pendiente')
    ->count();
echo "Total con docs en 'pendiente': {$pendientes}\n";

echo "\n=== VERIFICACION DE CONSISTENCIA DOCUMENTOS ===\n";
$todas = solicitudes::orderBy('id','desc')->take(50)->get();
$problemas = 0;
foreach($todas as $s) {
    $expectedPath = "solicitud_{$s->id}";
    $docFields = ['pachis', 'pacordmed', 'pacauto', 'pacdocid', 'soporte_patologia'];
    foreach($docFields as $field) {
        if ($s->$field && $s->$field !== 'pendiente' && $s->$field !== null) {
            if (strpos($s->$field, $expectedPath) === false) {
                echo "PROBLEMA: ID:{$s->id} campo:{$field} tiene ruta '{$s->$field}' (NO contiene '{$expectedPath}')\n";
                $problemas++;
            }
        }
    }
}
echo "Total problemas de consistencia: {$problemas}\n";

echo "\n=== SOLICITUDES DUPLICADAS POR solnum PARA MISMO USUARIO ===\n";
$duplicados = DB::select("
    SELECT pacid, solnum, COUNT(*) as cnt 
    FROM solicitudes 
    WHERE solnum > 0
    GROUP BY pacid, solnum 
    HAVING COUNT(*) > 1 
    ORDER BY cnt DESC 
    LIMIT 20
");
foreach($duplicados as $d) {
    echo "pacid:{$d->pacid} solnum:{$d->solnum} repeticiones:{$d->cnt}\n";
}
if (empty($duplicados)) {
    echo "No hay solnum duplicados\n";
}
