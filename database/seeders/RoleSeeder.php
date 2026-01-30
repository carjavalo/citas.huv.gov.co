<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role1 = Role::create(['name' => 'Administrador']);
        $role2 = Role::create(['name' => 'Consultor']);
        $role3 = Role::create(['name' => 'Usuario']);
        $role4 = Role::create(['name' => 'Super Admin']);


        Permission::create(
            [
            'name' => 'admin.usuarios.create',
            'desc' => 'Crear usuarios'
            ]
        )->assignRole($role1);

        Permission::create(
            [
            'name' => 'admin.usuarios.consult',
            'desc' => 'Consultar usuarios'
            ]
        )->assignRole($role1);

        Permission::create(
            [
            'name' => 'admin.usuarios.update',
            'desc' => 'Actualizar datos usuarios'
            ]
        )->assignRole($role1);

        Permission::create(
            [
            'name' => 'citas.consulta.solicitudes',
            'desc' => 'Consultar solicitudes de citas'
            ]
        )->assignRole($role1, $role2);

        Permission::create(
            [
            'name' => 'citas.consulta.agendar',
            'desc' => 'Agendar solicitudes de citas'
            ]
        )->assignRole($role1, $role2);

        Permission::create(
            [
            'name' => 'citas.consulta.notificar',
            'desc' => 'Notificar novedad solicitud de cita'
            ]
        )->assignRole($role1, $role2);

    }
}
