<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

// Verificar que el rol "Usuario" exista
$rolUsuario = Role::where('name', 'Usuario')->first();
if (!$rolUsuario) {
    echo "ERROR: El rol 'Usuario' no existe en la base de datos.\n";
    exit(1);
}

echo "Rol 'Usuario' encontrado con ID: {$rolUsuario->id}\n";

// Contar usuarios sin ningún rol
$sinRolCount = User::doesntHave('roles')->count();
echo "Usuarios sin ningún rol: $sinRolCount\n";

if ($sinRolCount === 0) {
    echo "No hay usuarios sin rol. Nada que hacer.\n";
    exit(0);
}

// Obtener IDs de usuarios sin rol
$sinRolIds = User::doesntHave('roles')->pluck('id');

// Determinar el guard del rol
$guard = $rolUsuario->guard_name ?? 'web';

// Insertar en lote en la tabla model_has_roles
$inserted = 0;
$chunks = $sinRolIds->chunk(1000);

foreach ($chunks as $chunk) {
    $data = [];
    foreach ($chunk as $userId) {
        $data[] = [
            'role_id' => $rolUsuario->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $userId,
        ];
    }
    DB::table('model_has_roles')->insert($data);
    $inserted += count($data);
    echo "  Progreso: $inserted / $sinRolCount\n";
}

echo "\n=== Completado ===\n";
echo "Usuarios asignados al rol 'Usuario': $inserted\n";
echo "Usuarios sin rol ahora: " . User::doesntHave('roles')->count() . "\n";
