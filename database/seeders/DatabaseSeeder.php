<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /* $this->call(RoleSeeder::class); */
        $this->call(PermisosSeeder::class);
        $this->call(EstadoSolicitudTableSeeder::class);
        $this->call(HospitalTableSeeder::class);
        $this->call(UsuariosSeeder::class);
    }
}
