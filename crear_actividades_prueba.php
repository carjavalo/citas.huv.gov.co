<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserActivity;

echo "=== CREANDO ACTIVIDADES DE PRUEBA ===\n\n";

// Obtener usuarios que NO sean Super Admin
$usuarios = User::whereDoesntHave('roles', function($query) {
    $query->where('name', 'Super Admin');
})->take(3)->get();

if ($usuarios->count() === 0) {
    echo "No hay usuarios disponibles para crear actividades de prueba.\n";
    exit;
}

$tiposActividad = [
    ['tipo' => 'login', 'descripcion' => 'Usuario inició sesión', 'modulo' => 'autenticacion'],
    ['tipo' => 'accion', 'descripcion' => 'Consultó solicitudes de citas', 'modulo' => 'citas'],
    ['tipo' => 'accion', 'descripcion' => 'Consultó lista de usuarios', 'modulo' => 'usuarios'],
    ['tipo' => 'accion', 'descripcion' => 'Accedió a configuración', 'modulo' => 'configuracion'],
    ['tipo' => 'logout', 'descripcion' => 'Usuario cerró sesión', 'modulo' => 'autenticacion'],
];

$count = 0;
foreach ($usuarios as $user) {
    echo "Creando actividades para: {$user->name} {$user->apellido1} (Doc: {$user->ndocumento})\n";
    
    foreach ($tiposActividad as $actividad) {
        UserActivity::create([
            'user_id' => $user->id,
            'tipo_actividad' => $actividad['tipo'],
            'descripcion' => $actividad['descripcion'],
            'modulo' => $actividad['modulo'],
            'accion' => $actividad['tipo'] === 'login' ? 'login' : ($actividad['tipo'] === 'logout' ? 'logout' : 'consultar'),
            'ip_address' => '192.168.2.' . rand(1, 254),
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);
        $count++;
    }
    echo "  ✓ {$count} actividades creadas\n";
}

echo "\n=== RESUMEN ===\n";
echo "Total de actividades creadas: {$count}\n";
echo "Ingresos: " . UserActivity::where('tipo_actividad', 'login')->count() . "\n";
echo "Salidas: " . UserActivity::where('tipo_actividad', 'logout')->count() . "\n";
echo "Acciones: " . UserActivity::where('tipo_actividad', 'accion')->count() . "\n";

echo "\n✓ Actividades de prueba creadas exitosamente\n";
echo "✓ Accede a: http://192.168.2.200:8000/reporte/ingresos\n\n";
