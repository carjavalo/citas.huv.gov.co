<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear el rol Coordinador si no existe
        if (!Role::where('name', 'Coordinador')->exists()) {
            $coordinador = Role::create(['name' => 'Coordinador', 'guard_name' => 'web']);
            
            // Asignar permisos existentes al rol Coordinador
            $permisos = [
                'admin.usuarios.consult',
                'admin.usuarios.update',
                'citas.consulta.solicitudes',
                'citas.consulta.agendar',
                'citas.consulta.notificar',
                'admin.servicios.consultar',
                'admin.reporte.agentes',
            ];
            
            foreach ($permisos as $permiso) {
                $permission = Permission::where('name', $permiso)->first();
                if ($permission) {
                    $coordinador->givePermissionTo($permission);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', 'Coordinador')->delete();
    }
};
