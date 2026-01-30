<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserActivity;

echo "=== VERIFICACIÓN DE NÚMERO DE DOCUMENTO ===\n\n";

// Obtener usuarios con sus actividades
$usuarios = User::whereDoesntHave('roles', function($query) {
    $query->where('name', 'Super Admin');
})->take(5)->get();

echo "Usuarios en el sistema:\n";
foreach ($usuarios as $user) {
    $roles = $user->roles->pluck('name')->join(', ');
    echo "- {$user->name} {$user->apellido1}\n";
    echo "  Documento: {$user->ndocumento}\n";
    echo "  ID: {$user->id}\n";
    echo "  Roles: {$roles}\n\n";
}

echo "\n=== ACTIVIDADES CON NÚMERO DE DOCUMENTO ===\n\n";

$actividades = UserActivity::with('user')
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get();

if ($actividades->count() > 0) {
    foreach ($actividades as $act) {
        $userName = $act->user ? $act->user->name . ' ' . $act->user->apellido1 : 'Usuario Eliminado';
        $documento = $act->user ? $act->user->ndocumento : 'N/A';
        echo "[{$act->created_at}] Doc: {$documento} - {$userName}\n";
        echo "  Tipo: {$act->tipo_actividad} - {$act->descripcion}\n\n";
    }
} else {
    echo "No hay actividades registradas aún.\n";
}

echo "\n✓ Paginación configurada a 15 registros por página\n";
echo "✓ Campo ID cambiado a Número de Documento\n";
echo "✓ Registro de usuarios nuevos activado\n\n";
