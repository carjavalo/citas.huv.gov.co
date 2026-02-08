<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

// Verificar que el rol "Usuario" exista
$rolUsuario = Role::where('name', 'Usuario')->first();
if (!$rolUsuario) {
    echo "ERROR: El rol 'Usuario' no existe en la base de datos.\n";
    exit(1);
}

// Usuarios sin ningún rol
$sinRol = User::doesntHave('roles')->get();
echo "Usuarios sin ningún rol: " . $sinRol->count() . "\n";

$actualizados = 0;
foreach ($sinRol as $user) {
    $user->assignRole('Usuario');
    $actualizados++;
}

echo "Usuarios actualizados a rol 'Usuario': $actualizados\n";

// Verificación final
$sinRolFinal = User::doesntHave('roles')->count();
echo "Usuarios sin rol después de actualización: $sinRolFinal\n";
