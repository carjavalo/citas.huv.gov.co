<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * "Importar usuarios" (Consultar usuarios → otras acciones) se administra en el
 * Gestor de permisos. No se asigna a ningún rol: hasta configurarlo, el botón
 * solo lo ve Super Admin (Gate::before le concede todo).
 */
class PermisoImportarUsuarios extends Migration
{
    public function up()
    {
        Permission::firstOrCreate(
            ['name' => 'admin.usuarios.importar', 'guard_name' => 'web'],
            ['desc' => 'Importar usuarios desde un archivo de Excel']
        );
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down()
    {
        Permission::where('name', 'admin.usuarios.importar')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
