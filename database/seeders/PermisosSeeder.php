<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Permission::create(
            [
            'name' => 'admin.eps.consultar',
            'desc' => 'Consultar las eps registradas'
            ]
        )->assignRole(1);

        Permission::create(
            [
            'name' => 'admin.eps.edit',
            'desc' => 'Editar las eps registradas'
            ]
        )->assignRole(1); */
        $rolHospitalAhijado = Role::create(['name' => 'Hospital']);
        $gestorObstetricia = Role::create(['name' => 'Gestor Obstetricia']);
        $makeRequest = Permission::create(
            [
                'name' => 'godson.request.make',
                'desc' => 'Registrar solicitud obstetricia de alto riesgo'
            ]
        )->assignRole(1);
        $viewRequest = Permission::create(
            [
                'name' => 'godson.request.view',
                'desc' => 'Consultar las solicitudes hechas por Hospitales Ahijados'
            ]
        )->assignRole(1);
        $attendRequest = Permission::create(
            [
                'name' => 'godson.request.attend',
                'desc' => 'Atender las solicitudes hechas por Hospitales Ahijados'
            ]
        )->assignRole(1);
        $rejectRequest = Permission::create(
            [
                'name' => 'godson.request.reject',
                'desc' => 'Rechazar las solicitudes hechas por Hospitales Ahijados'
            ]
        )->assignRole(1);
        $checkRequest = Permission::create(
            [
                'name' => 'godson.request.check',
                'desc' => 'Cambiar estado a revisión a solicitudes hechas por Hospitales Ahijados'
            ]
        )->assignRole(1);
        $cancelRequest = Permission::create(
            [
                'name' => 'godson.request.cancel',
                'desc' => 'Cancelar la solicitud hecha por Hospital Ahijado'
            ]
        )->assignRole(1);
        $rolHospitalAhijado->syncPermissions([$viewRequest, $makeRequest, $cancelRequest]);
        $gestorObstetricia->syncPermissions([$rejectRequest, $checkRequest, $attendRequest, $viewRequest]);
    }
}
