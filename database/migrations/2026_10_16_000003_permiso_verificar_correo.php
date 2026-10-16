<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * "Autorizar verificación de correo" (Consultar usuarios → Editar) deja de
 * estar fija para Super Admin y pasa a administrarse en el Gestor de
 * permisos. No se asigna a ningún rol: hasta configurarlo, solo Super Admin.
 */
class PermisoVerificarCorreo extends Migration
{
    public function up()
    {
        Permission::firstOrCreate(
            ['name' => 'admin.usuarios.verificar_correo', 'guard_name' => 'web'],
            ['desc' => 'Autorizar la verificación del correo de un usuario']
        );
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down()
    {
        Permission::where('name', 'admin.usuarios.verificar_correo')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
