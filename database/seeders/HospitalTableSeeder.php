<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Hospital::create([
            "descripcion"   => "ESE RED CENTRO",
            "email"         => "intrahospitalaria@saludcentro.gov.co",
            "activo"        => true
        ]);
        Hospital::create([
            "descripcion"   => "ESE RED LADERA",
            "email"         => "programaprenatal@saludladera.gov.co",
            "activo"        => true
        ]);
        Hospital::create([
            "descripcion"   => "ESE RED NORTE",
            "email"         => "Hjpbpartos@gmail.com",
            "activo"        => true
        ]);
        Hospital::create([
            "descripcion"   => "ESE RED ORIENTE",
            "email"         => "partos.rso@redoriente.gov.co",
            "activo"        => true
        ]);
        Hospital::create([
            "descripcion"   => "ESE RED SURORIENTE",
            "email"         => "referenciapaciente1.suroriente@gmail.com",
            "activo"        => true
        ]);
    }
}
