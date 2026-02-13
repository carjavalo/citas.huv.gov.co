<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DIAGNÓSTICO COMPLETO DE AUTO_INCREMENT en solicitudes ===" . PHP_EOL . PHP_EOL;

// 1. Verificar estructura de la tabla
echo "--- 1. Estructura de la columna id ---" . PHP_EOL;
$columns = DB::select("SHOW COLUMNS FROM solicitudes WHERE Field = 'id'");
foreach ($columns as $col) {
    echo "  Field: {$col->Field}" . PHP_EOL;
    echo "  Type: {$col->Type}" . PHP_EOL;
    echo "  Null: {$col->Null}" . PHP_EOL;
    echo "  Key: {$col->Key}" . PHP_EOL;
    echo "  Default: " . var_export($col->Default, true) . PHP_EOL;
    echo "  Extra: {$col->Extra}" . PHP_EOL;
}

$hasAutoIncrement = !empty($columns) && strpos($columns[0]->Extra, 'auto_increment') !== false;
echo PHP_EOL . "  ¿AUTO_INCREMENT activo?: " . ($hasAutoIncrement ? 'SÍ' : '** NO **') . PHP_EOL;

// 2. Verificar el valor actual de AUTO_INCREMENT
echo PHP_EOL . "--- 2. Valor AUTO_INCREMENT en tabla ---" . PHP_EOL;
$tableStatus = DB::select("SHOW TABLE STATUS LIKE 'solicitudes'");
echo "  Auto_increment: " . ($tableStatus[0]->Auto_increment ?? 'NULL') . PHP_EOL;
echo "  MAX(id) actual: " . DB::table('solicitudes')->max('id') . PHP_EOL;
echo "  Total registros: " . DB::table('solicitudes')->count() . PHP_EOL;

// 3. Verificar registros con id = 0 o NULL
echo PHP_EOL . "--- 3. Registros problemáticos ---" . PHP_EOL;
$conIdCero = DB::table('solicitudes')->where('id', 0)->count();
$conIdNull = DB::select("SELECT COUNT(*) as cnt FROM solicitudes WHERE id IS NULL")[0]->cnt;
echo "  Con id = 0: {$conIdCero}" . PHP_EOL;
echo "  Con id = NULL: {$conIdNull}" . PHP_EOL;

// 4. Verificar triggers
echo PHP_EOL . "--- 4. Triggers en solicitudes ---" . PHP_EOL;
$triggers = DB::select("SHOW TRIGGERS LIKE 'solicitudes'");
if (empty($triggers)) {
    echo "  Ningún trigger encontrado" . PHP_EOL;
} else {
    foreach ($triggers as $trigger) {
        echo "  Trigger: {$trigger->Trigger} - Timing: {$trigger->Timing} - Event: {$trigger->Event}" . PHP_EOL;
        echo "  Statement: {$trigger->Statement}" . PHP_EOL;
    }
}

// 5. Verificar propiedades del modelo
echo PHP_EOL . "--- 5. Propiedades del modelo solicitudes ---" . PHP_EOL;
$model = new \App\Models\solicitudes();
echo "  primaryKey: " . $model->getKeyName() . PHP_EOL;
echo "  incrementing: " . ($model->getIncrementing() ? 'true' : 'false') . PHP_EOL;
echo "  keyType: " . $model->getKeyType() . PHP_EOL;
echo "  fillable: " . json_encode($model->getFillable()) . PHP_EOL;
echo "  guarded: " . json_encode($model->getGuarded()) . PHP_EOL;

// 6. PRUEBA DE INSERCIÓN REAL
echo PHP_EOL . "--- 6. PRUEBA DE INSERCIÓN ---" . PHP_EOL;
DB::enableQueryLog();

$sol = \App\Models\solicitudes::create([
    'pacid'     => DB::table('users')->value('id'),
    'espec'     => 'TEST_DIAG',
    'estado'    => 'TEST_AUTO_INCREMENT',
    'solnum'    => 99999,
    'pachis'    => 'test_diag',
    'pacordmed' => 'test_diag',
    'pacdocid'  => 'test_diag',
]);

$queries = DB::getQueryLog();
echo "  Query ejecutada: " . json_encode(end($queries)) . PHP_EOL;
echo "  ID retornado por Eloquent (->id): " . var_export($sol->id, true) . PHP_EOL;
echo "  getKey(): " . var_export($sol->getKey(), true) . PHP_EOL;
echo "  exists: " . var_export($sol->exists, true) . PHP_EOL;
echo "  wasRecentlyCreated: " . var_export($sol->wasRecentlyCreated, true) . PHP_EOL;

// 7. Verificar directamente en la BD
$directCheck = DB::select("SELECT id FROM solicitudes WHERE estado = 'TEST_AUTO_INCREMENT' ORDER BY id DESC LIMIT 1");
echo "  ID en la BD (consulta directa): " . ($directCheck[0]->id ?? 'NO ENCONTRADO') . PHP_EOL;

// 8. Limpiar registro de prueba
DB::table('solicitudes')->where('estado', 'TEST_AUTO_INCREMENT')->delete();
echo PHP_EOL . "  (Registro de prueba eliminado)" . PHP_EOL;

echo PHP_EOL . "=== FIN DIAGNÓSTICO ===" . PHP_EOL;
