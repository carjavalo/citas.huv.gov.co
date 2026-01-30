<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;


class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* User::create([
            'name'  => 'Coordinación',
            'email' => 'coordinacionuba@huv.gov.co',
            'apellido1' => 'UBA',
            'tdocumento'    => ' ',
            'ndocumento'    => 123,
            'telefono1'     => ' ',
            'eps'           => '60',
            'password'      => bcrypt('12345678'),
        ])->assignRole('Administrador');

        User::create([
            'name'  => 'Jhan Sebastian',
            'email' => 'jhrodriguez@huv.gov.co',
            'apellido1' => 'Rodriguez',
            'apellido2' => 'Desarrollador',
            'tdocumento'    => 'CC',
            'ndocumento'    => 1004702303,
            'telefono1'     => '3146212853',
            'eps'           => '60',
            'password'      => bcrypt('12345678'),
        ])->assignRole('Administrador'); */

        User::create([
            'name'  => 'ESE RED CENTRO',
            'email' => 'intrahospitalaria@saludcentro.gov.co',
            'apellido1' => '',
            'apellido2' => '',
            'tdocumento'    => '1',
            'ndocumento'    => 1231,
            'telefono1'     => '',
            'eps'           => '0',
            'password'      => bcrypt('12345'),
        ])->assignRole('Hospital');
        User::create([
            'name'  => 'ESE RED LADERA',
            'email' => 'programaprenatal@saludladera.gov.co',
            'apellido1' => '',
            'apellido2' => '',
            'tdocumento'    => '1',
            'ndocumento'    => 1232,
            'telefono1'     => '',
            'eps'           => '0',
            'password'      => bcrypt('12345'),
        ])->assignRole('Hospital');
        User::create([
            'name'  => 'ESE RED NORTE',
            'email' => 'Hjpbpartos@gmail.com',
            'apellido1' => '',
            'apellido2' => '',
            'tdocumento'    => '1',
            'ndocumento'    => 1233,
            'telefono1'     => '',
            'eps'           => '0',
            'password'      => bcrypt('12345'),
        ])->assignRole('Hospital');
        User::create([
            'name'  => 'ESE RED ORIENTE',
            'email' => 'partos.rso@redoriente.gov.co',
            'apellido1' => '',
            'apellido2' => '',
            'tdocumento'    => '1',
            'ndocumento'    => 1234,
            'telefono1'     => '',
            'eps'           => '0',
            'password'      => bcrypt('12345'),
        ])->assignRole('Hospital');
        User::create([
            'name'  => 'ESE RED SURORIENTE',
            'email' => 'referenciapaciente1.suroriente@gmail.com',
            'apellido1' => '',
            'apellido2' => '',
            'tdocumento'    => '1',
            'ndocumento'    => 1235,
            'telefono1'     => '',
            'eps'           => '0',
            'password'      => bcrypt('12345'),
        ])->assignRole('Hospital');
    }
}
