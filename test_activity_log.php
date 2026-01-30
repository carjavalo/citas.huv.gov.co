<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserActivity;

echo "=== SISTEMA DE AUDITORÍA DE ACTIVIDADES ===\n\n";

// Obtener usuarios que NO sean Super Admin
$usuarios = User::whereDoesntHave('roles', function($query) {
    $query->where('name', 'Super Admin');
})->take(5)->get();

echo "Usuarios encontrados (sin Super Admin): " . $usuarios->count() . "\n\n";

foreach ($usuarios as $user) {
    $roles = $user->roles->pluck('name')->join(', ');
    echo "- {$user->name} {$user->apellido1} (ID: {$user->id}) - Roles: {$roles}\n";
}

echo "\n=== ACTIVIDADES REGISTRADAS ===\n\n";

$actividades = UserActivity::with('user')
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get();

if ($actividades->count() > 0) {
    foreach ($actividades as $act) {
        $userName = $act->user ? $act->user->name . ' ' . $act->user->apellido1 : 'Usuario Eliminado';
        echo "[{$act->created_at}] {$userName} - {$act->tipo_actividad}: {$act->descripcion}\n";
    }
} else {
    echo "No hay actividades registradas aún.\n";
    echo "\nEl sistema comenzará a registrar actividades cuando:\n";
    echo "1. Los usuarios inicien sesión (login)\n";
    echo "2. Los usuarios cierren sesión (logout)\n";
    echo "3. Se registren nuevos usuarios\n";
    echo "4. Se soliciten citas\n";
    echo "5. Los usuarios realicen cualquier acción en el sistema\n";
}

echo "\n=== ESTADÍSTICAS ===\n\n";
echo "Total de actividades: " . UserActivity::count() . "\n";
echo "Ingresos (login): " . UserActivity::where('tipo_actividad', 'login')->count() . "\n";
echo "Salidas (logout): " . UserActivity::where('tipo_actividad', 'logout')->count() . "\n";
echo "Registros: " . UserActivity::where('tipo_actividad', 'registro')->count() . "\n";
echo "Citas: " . UserActivity::where('tipo_actividad', 'cita')->count() . "\n";
echo "Acciones: " . UserActivity::where('tipo_actividad', 'accion')->count() . "\n";

echo "\n✓ Sistema de auditoría instalado correctamente\n";
echo "✓ Accede a: http://192.168.2.200:8000/reporte/ingresos\n\n";
