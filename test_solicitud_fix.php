<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\solicitudes;

// Verificar estado AUTO_INCREMENT
$result = DB::select('SHOW CREATE TABLE solicitudes');
$createTable = $result[0]->{'Create Table'};
preg_match('/AUTO_INCREMENT=(\d+)/', $createTable, $matches);
echo 'AUTO_INCREMENT actual: ' . ($matches[1] ?? 'NO ENCONTRADO') . PHP_EOL;

// Verificar max ID
$maxId = DB::table('solicitudes')->max('id');
echo 'Max ID en tabla: ' . $maxId . PHP_EOL;

// Verificar migración
$migrationRan = DB::table('migrations')->where('migration', 'like', '%fix_solicitudes_auto_increment%')->exists();
echo 'Migración fix AUTO_INCREMENT ejecutada: ' . ($migrationRan ? 'SI' : 'NO') . PHP_EOL;

// Test: crear y borrar un registro para verificar que id se asigna correctamente
echo PHP_EOL . '=== TEST DE CREACION ===' . PHP_EOL;
// Obtener un usuario real para el test
$realUser = DB::table('users')->first();
echo 'Usuario de test: ID=' . $realUser->id . ' email=' . $realUser->email . PHP_EOL;

DB::beginTransaction();
try {
    $test = solicitudes::create([
        'pacid' => $realUser->id,
        'espec' => '999',
        'estado' => 'TEST',
        'solnum' => 9999,
        'pachis' => 'test',
        'pacordmed' => 'test',
        'pacdocid' => 'test',
    ]);
    echo 'ID retornado por create(): ' . var_export($test->id, true) . PHP_EOL;
    echo 'solnum retornado: ' . var_export($test->solnum, true) . PHP_EOL;
    echo 'Tipo de ID: ' . gettype($test->id) . PHP_EOL;
    echo 'ID es mayor que maxId (' . $maxId . '): ' . ($test->id > $maxId ? 'SI' : 'NO') . PHP_EOL;
    echo 'ID es el que esperamos (' . ($maxId + 1) . '): ' . ($test->id == $maxId + 1 ? 'SI' : 'NO') . PHP_EOL;
} finally {
    DB::rollBack();
    echo 'Test rollback exitoso - registro eliminado' . PHP_EOL;
}

// Verificar que no hay solnum con valor 0 o null
$sinSolnum = solicitudes::where('solnum', 0)->orWhereNull('solnum')->count();
echo PHP_EOL . 'Solicitudes con solnum=0 o NULL: ' . $sinSolnum . PHP_EOL;

// Verificar la vista solicitar para ver los campos del formulario
echo PHP_EOL . '=== VERIFICACION DISCO UPLOAD ===' . PHP_EOL;
$uploadDisk = config('filesystems.disks.upload');
echo 'Disco upload configurado: ' . ($uploadDisk ? json_encode($uploadDisk) : 'NO EXISTE') . PHP_EOL;
