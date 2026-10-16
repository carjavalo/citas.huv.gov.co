<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Gestión Roles (CRUD de roles):
 * - descripcion: texto libre para identificar el rol.
 * - requiere_sede: el rol trabaja con sede y servicio (al editar un usuario
 *   con este rol se le pide sede y servicio). Antes estaba escrito en el
 *   código para Consultor, Coordinador y Administrador.
 * - Permisos de la vista, administrables desde el Gestor de permisos. No se
 *   asignan a ningún rol: hasta configurarlos, solo Super Admin entra.
 */
class GestionRoles extends Migration
{
    public const PERMISOS = [
        'admin.roles.gestionar' => 'Ver la gestión de roles',
        'admin.roles.crear' => 'Crear roles',
        'admin.roles.editar' => 'Editar roles',
        'admin.roles.eliminar' => 'Eliminar roles',
    ];

    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('descripcion', 255)->nullable()->after('guard_name');
            $table->boolean('requiere_sede')->default(false)->after('descripcion');
        });

        DB::table('roles')
            ->whereIn('name', ['Consultor', 'Coordinador', 'Administrador'])
            ->update(['requiere_sede' => true]);

        foreach (self::PERMISOS as $nombre => $desc) {
            Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web'], ['desc' => $desc]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down()
    {
        Permission::whereIn('name', array_keys(self::PERMISOS))->delete();

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'requiere_sede']);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
