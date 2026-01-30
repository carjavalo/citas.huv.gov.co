<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = Spatie\Permission\Models\Role::where('name', 'Administrador')->first();
if ($role) {
    $permissions = $role->permissions->pluck('name')->toArray();
    echo "Permisos del rol Administrador:\n";
    foreach ($permissions as $perm) {
        echo "- $perm\n";
    }
} else {
    echo "Rol Administrador no encontrado.\n";
}
