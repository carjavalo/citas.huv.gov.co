<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosReporteAgenteSeeder extends Seeder
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
            'name' => 'admin.reporte.agentes',
            'desc' => 'Consultar reporte de agentes'
            ]
        )->assignRole(1);
        
        Permission::create(
            [
            'name' => 'admin.reporte.agentes.exportar.hoy',
            'desc' => 'Exportar reporte de agentes dia actual'
            ]
        )->assignRole(1);

        Permission::create(
            [
            'name' => 'admin.reporte.agentes.exportar.filtro_fecha',
            'desc' => 'Exportar reporte de agentes filtrado por fecha'
            ]
        )->assignRole(1);
    }
}
