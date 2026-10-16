<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Roles que cada rol puede asignar al crear o editar usuarios. Lo administra
 * el Gestor de permisos. Se siembra con las reglas que estaban escritas en
 * Usuarios\Consulta (getRolesVisibles / getRolesRestringidos) para que nadie
 * gane ni pierda alcance. Super Admin no tiene filas: asigna cualquier rol.
 */
class CreateRoleRolesAsignablesTable extends Migration
{
    public function up()
    {
        Schema::create('role_roles_asignables', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('rol_asignable_id');
            $table->primary(['role_id', 'rol_asignable_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('rol_asignable_id')->references('id')->on('roles')->onDelete('cascade');
        });

        $roles = DB::table('roles')->pluck('id', 'name');

        // Roles que NO podía asignar cada rol según las reglas anteriores.
        $restringidos = [
            'Administrador' => ['Super Admin', 'Administrador'],
            'Coordinador' => ['Super Admin', 'Administrador', 'Gestor Obstetricia', 'Hospital'],
        ];
        $porDefecto = ['Super Admin', 'Coordinador'];

        foreach ($roles as $nombre => $id) {
            if ($nombre === 'Super Admin') {
                continue;
            }
            $excluir = $restringidos[$nombre] ?? $porDefecto;
            foreach ($roles as $nombreAsignable => $idAsignable) {
                if (!in_array($nombreAsignable, $excluir, true)) {
                    DB::table('role_roles_asignables')->insert([
                        'role_id' => $id,
                        'rol_asignable_id' => $idAsignable,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('role_roles_asignables');
    }
}
