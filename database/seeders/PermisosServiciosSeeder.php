<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(
            [
            'name' => 'admin.servicios.consultar',
            'desc' => 'Consultar los servicios registrados'
            ]
        )->assignRole(1);

        Permission::create(
            [
            'name' => 'admin.servicios.edit',
            'desc' => 'Editar los servicios registrados'
            ]
        )->assignRole(1);
    }
}
