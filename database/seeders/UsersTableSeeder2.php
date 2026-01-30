<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder2 extends Seeder
{
    public function run()
    {
        $usuarios = [
            ['id' => 9, 'name' => 'imagenesdiagnosticas', 'apellido1' => 'diagnosticas', 'apellido2' => 'prueba', 'tdocumento' => 1, 'ndocumento' => '31313131', 'telefono1' => '1111222233', 'telefono2' => null, 'eps' => 6, 'email' => 'citasimagenesidx@correohuv.gov.co', 'email_verified_at' => '2022-02-21 08:20:01', 'password' => '$2y$10$S5Jn67n8hlbktjnitcJJ6ON8.QOG8z0.4EoQhSgZUm/oaKx3mojee', 'created_at' => '2022-02-21 08:17:01', 'updated_at' => '2022-02-21 08:17:01'],
            ['id' => 50, 'name' => 'Martha Cecilia', 'apellido1' => 'Cano', 'apellido2' => 'Franco', 'tdocumento' => 1, 'ndocumento' => '31996410', 'telefono1' => '3153018572', 'telefono2' => null, 'eps' => 134, 'email' => 'macecafra@gmail.com', 'email_verified_at' => '2022-03-04 18:49:16', 'password' => '$2y$10$TlSowa73bs8ip94dn1Vai.WaLNlaC6N/0rR7DtJS4Wvp0x2OjvsGW', 'created_at' => '2022-03-04 18:48:07', 'updated_at' => '2022-03-04 18:49:16'],
            ['id' => 31, 'name' => 'eduar mauricio', 'apellido1' => 'quintana', 'apellido2' => 'escobar', 'tdocumento' => 1, 'ndocumento' => '1130607944', 'telefono1' => '3017283544', 'telefono2' => null, 'eps' => 72, 'email' => 'eduarmauricio33@gmail.com', 'email_verified_at' => '2022-03-05 08:44:25', 'password' => '$2y$10$3gaoaCOgOukCuEa061OmtOoVaFswfnvAzGcWlwG.benXEbD9Dr6mK', 'created_at' => '2022-03-02 16:06:52', 'updated_at' => '2022-03-05 08:44:25'],
            ['id' => 32, 'name' => 'Raquel', 'apellido1' => 'Lenis', 'apellido2' => 'Garcia', 'tdocumento' => 1, 'ndocumento' => '66830269', 'telefono1' => '3217813125', 'telefono2' => null, 'eps' => 72, 'email' => 'a111@comunicacionesdiezdemarzo.com', 'email_verified_at' => '2022-03-05 09:08:34', 'password' => '$2y$10$2YI2fO.xE8eSTswVdAZl1OJE9/6W4mHyWoPaOSpFIXx5gm3OLvHxC', 'created_at' => '2022-03-02 16:09:00', 'updated_at' => '2022-03-05 09:08:34'],
            ['id' => 51, 'name' => 'Jeison Andres', 'apellido1' => 'Lora', 'apellido2' => 'Muñoz', 'tdocumento' => 1, 'ndocumento' => '1144128224', 'telefono1' => '3178025619', 'telefono2' => null, 'eps' => 147, 'email' => 'jeyingindustrial@hotmail.com', 'email_verified_at' => '2022-03-05 09:18:12', 'password' => '$2y$10$P9X8JlBZMNrLqNelLlVa7eMSxgov34doxL3TJe0Vqb./f55nkTPse', 'created_at' => '2022-03-05 09:17:36', 'updated_at' => '2022-03-05 09:18:12'],
            ['id' => 37, 'name' => 'Jorge andres', 'apellido1' => 'Martinez', 'apellido2' => 'Pineda', 'tdocumento' => 1, 'ndocumento' => '94455350', 'telefono1' => '3187991289', 'telefono2' => '3154092254', 'eps' => 60, 'email' => 'martinezcanas1975@hotmail.com', 'password' => '$2y$10$sX4xtB2oxuWlqtFUpoOx3ecu3Lz0nEnUoC5mEjjmUMkcsi0ELMgJq', 'created_at' => '2022-03-03 10:45:38', 'updated_at' => '2022-03-03 10:45:38'],
            ['id' => 53, 'name' => 'Luz Dary', 'apellido1' => 'Calero', 'apellido2' => 'Solis', 'tdocumento' => 1, 'ndocumento' => '29541343', 'telefono1' => '3217747825', 'telefono2' => '3126365562', 'eps' => 134, 'email' => 'calerosolisluzdary@gmail.com', 'email_verified_at' => '2022-03-07 09:46:04', 'password' => '$2y$10$YUbSIJRKM9bEiBzMMCjxR.raDi.k9WaM39daeWzud3V9xOwxYjWOS', 'created_at' => '2022-03-07 09:40:41', 'updated_at' => '2022-03-07 09:46:04'],
            ['id' => 34, 'name' => 'Adriana', 'apellido1' => 'Diaz', 'apellido2' => 'Pelaez', 'tdocumento' => 1, 'ndocumento' => '66905551', 'telefono1' => '3226350032', 'telefono2' => null, 'eps' => 72, 'email' => 'aadiaz2873@gmail.com', 'email_verified_at' => '2022-03-09 09:23:37', 'password' => '$2y$10$VKpQaqFYHMN6dZjqbT9sOuXcVE.QLOmceHL30mVWmaUsuXrTb4qBK', 'created_at' => '2022-03-02 16:11:59', 'updated_at' => '2022-03-09 09:23:37'],
            ['id' => 55, 'name' => 'leonardo', 'apellido1' => 'sanchez', 'apellido2' => 'escobar', 'tdocumento' => 1, 'ndocumento' => '1143937472', 'telefono1' => '3245082284', 'telefono2' => '3154914398', 'eps' => 83, 'email' => 'leo20-14@hotmail.com', 'email_verified_at' => '2022-03-07 11:12:40', 'password' => '$2y$10$5sz1dV3LKQNNSg7VY4QxxOe4xerincR1rJkggxnSS5O72RDArRcau', 'created_at' => '2022-03-07 11:11:48', 'updated_at' => '2022-03-07 11:12:40'],
            ['id' => 56, 'name' => 'EDUAR', 'apellido1' => 'RIOS', 'apellido2' => null, 'tdocumento' => 1, 'ndocumento' => '16823782', 'telefono1' => '3167970882', 'telefono2' => '3176229690', 'eps' => 60, 'email' => 'larirav12@gmail.com', 'email_verified_at' => '2022-03-07 11:51:25', 'password' => '$2y$10$wrE9f1ogG1a.e4fRv5TEeuXlT44zqKZoOBgoptpABYR42Eveiy132', 'created_at' => '2022-03-07 11:50:33', 'updated_at' => '2022-03-07 11:51:25'],
        ];

        foreach ($usuarios as $userData) {
            $user = User::updateOrCreate(['id' => $userData['id']], $userData);
            if (!$user->hasRole('Usuario')) {
                $user->assignRole('Usuario');
            }
        }

        $this->command->info('Lote 1: ' . count($usuarios) . ' usuarios insertados.');
    }
}
