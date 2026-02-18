<?php
// Rutina de verificación de integridad de solicitudes
// - Verifica consecutivos duplicados o faltantes
// - Verifica vinculación de documentos adjuntos

require_once 'app/Models/solicitudes.php';
require_once 'app/Models/User.php';

use App\Models\solicitudes;
use Illuminate\Database\Capsule\Manager as DB;

// Configuración básica para ejecución standalone
if (!class_exists('DB')) {
    require 'vendor/autoload.php';
    $capsule = new DB();
    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'citas',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
}

function verificarConsecutivos() {
    $usuarios = DB::table('users')->pluck('id');
    $errores = [];
    foreach ($usuarios as $uid) {
        $sols = DB::table('solicitudes')->where('pacid', $uid)->orderBy('solnum')->pluck('solnum');
        $prev = 0;
        foreach ($sols as $solnum) {
            if ($solnum != $prev + 1) {
                $errores[] = "Usuario $uid: consecutivo incorrecto en $solnum (esperado: " . ($prev + 1) . ")";
            }
            $prev = $solnum;
        }
    }
    return $errores;
}

function verificarDocumentos() {
    $errores = [];
    $solicitudes = DB::table('solicitudes')->get();
    foreach ($solicitudes as $sol) {
        $docFields = ['certfdo_cita','pachis','pacdocid','pacauto','pacordmed'];
        foreach ($docFields as $field) {
            if (!empty($sol->$field)) {
                $ruta = 'public/' . $sol->$field;
                if (!file_exists($ruta)) {
                    $errores[] = "Solicitud ID {$sol->id}: documento $field no encontrado en $ruta";
                }
            }
        }
    }
    return $errores;
}

// Ejecución
$consecutivos = verificarConsecutivos();
$documentos = verificarDocumentos();

if (empty($consecutivos) && empty($documentos)) {
    echo "No se detectaron errores de consecutivo ni de vinculación de documentos.\n";
} else {
    echo "Errores de consecutivo:\n";
    foreach ($consecutivos as $err) echo $err . "\n";
    echo "Errores de vinculación de documentos:\n";
    foreach ($documentos as $err) echo $err . "\n";
}
