<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = Spatie\Permission\Models\Role::where('name', 'Administrador')->first();

$permissionsNeeded = [
    'citas.consulta.solicitudes',
    'citas.consulta.agendar',
    'admin.usuarios.consult',
    'admin.eps.consultar',
    'admin.servicios.consultar',
    'admin.reporte.agentes',
    'godson.request.view',
    'godson.request.make',
    'godson.request.attend',
    'godson.request.check',
    'godson.request.reject',
    'godson.request.cancel',
];

if ($role) {
    foreach ($permissionsNeeded as $permName) {
        $permission = Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        if (!$role->hasPermissionTo($permName)) {
            $role->givePermissionTo($permission);
            echo "Permiso '$permName' asignado.\n";
        } else {
            echo "Permiso '$permName' ya existe.\n";
        }
    }
    echo "\nTodos los permisos han sido verificados/asignados al rol Administrador.\n";
} else {
    echo "Rol Administrador no encontrado.\n";
}
