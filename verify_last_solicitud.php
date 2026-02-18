<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$s = App\Models\solicitudes::orderBy('id','desc')->first();
echo "=== ÚLTIMA SOLICITUD CREADA ===\n";
echo "ID: {$s->id}\n";
echo "pacid: {$s->pacid}\n";
echo "solnum: {$s->solnum}\n";
echo "estado: {$s->estado}\n";
echo "espec: {$s->espec}\n";
echo "pachis: {$s->pachis}\n";
echo "pacordmed: {$s->pacordmed}\n";
echo "pacauto: " . ($s->pacauto ?? 'NULL') . "\n";
echo "pacdocid: {$s->pacdocid}\n";
echo "soporte_patologia: " . ($s->soporte_patologia ?? 'NULL') . "\n";
echo "created_at: {$s->created_at}\n\n";

// Verificar consistencia: la ruta debe contener solicitud_{id}
$expectedPath = "solicitud_{$s->id}";
$fields = ['pachis', 'pacordmed', 'pacdocid', 'pacauto', 'soporte_patologia'];
echo "=== VERIFICACIÓN DE RUTAS ===\n";
$allOk = true;
foreach ($fields as $f) {
    $path = $s->$f;
    if ($path && $path !== 'pendiente') {
        $hasCorrectId = strpos($path, $expectedPath) !== false;
        $fileExists = file_exists(__DIR__ . '/public/' . $path);
        echo "{$f}: " . ($hasCorrectId ? "✓ ID correcto" : "✗ ID INCORRECTO") 
             . " | Archivo: " . ($fileExists ? "✓ Existe" : "✗ NO EXISTE")
             . " | {$path}\n";
        if (!$hasCorrectId || !$fileExists) $allOk = false;
    }
}

// Verificar prefijos
echo "\n=== VERIFICACIÓN DE PREFIJOS ===\n";
echo "pachis tiene HC_: " . (strpos(basename($s->pachis), 'HC_') === 0 ? "✓ SI" : "✗ NO") . "\n";
echo "pacordmed tiene OM_: " . (strpos(basename($s->pacordmed), 'OM_') === 0 ? "✓ SI" : "✗ NO") . "\n";
echo "pacdocid tiene DI_: " . (strpos(basename($s->pacdocid), 'DI_') === 0 ? "✓ SI" : "✗ NO") . "\n";

// Verificar que NO son idénticas entre sí
echo "\n=== VERIFICACIÓN DE UNICIDAD ===\n";
echo "pachis != pacordmed: " . ($s->pachis !== $s->pacordmed ? "✓ DIFERENTES" : "✗ IGUALES") . "\n";
echo "pachis != pacdocid: " . ($s->pachis !== $s->pacdocid ? "✓ DIFERENTES" : "✗ IGUALES") . "\n";
echo "pacordmed != pacdocid: " . ($s->pacordmed !== $s->pacdocid ? "✓ DIFERENTES" : "✗ IGUALES") . "\n";

echo "\n=== RESULTADO ===\n";
echo $allOk ? "✓ TODO CORRECTO - La solicitud se creó correctamente con ID único y documentos vinculados\n" : "✗ HAY PROBLEMAS - Revisar los errores arriba\n";

// También mostrar los últimos solnum del usuario para verificar consecutivo
echo "\n=== CONSECUTIVOS DEL USUARIO {$s->pacid} ===\n";
$userSolicitudes = App\Models\solicitudes::where('pacid', $s->pacid)
    ->orderBy('solnum', 'asc')
    ->get(['id', 'solnum', 'created_at']);
foreach ($userSolicitudes as $us) {
    echo "ID:{$us->id} solnum:{$us->solnum} created:{$us->created_at}\n";
}
