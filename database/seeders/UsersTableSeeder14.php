<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder14 extends Seeder
{
    public function run()
    {
        $usuarios = [
            ['id' => 319, 'name' => 'LINA MARCELA', 'apellido1' => 'ASTUDILLO', 'apellido2' => 'MORENO', 'tdocumento' => 1, 'ndocumento' => '1143859629', 'telefono1' => '3176170989', 'telefono2' => '3165624665', 'eps' => 85, 'email' => 'LINA0594@HOTMAIL.COM', 'email_verified_at' => '2022-03-17 16:42:10', 'password' => '$2y$10$VSBina/2iW5.exRAZS331.0/fTFhsMeaEqh266erDefeu8Mlo.esG', 'created_at' => '2022-03-17 16:38:36', 'updated_at' => '2022-03-30 14:25:28'],
            ['id' => 329, 'name' => 'ELIZABETH', 'apellido1' => 'MAYA', 'apellido2' => 'DORADO', 'tdocumento' => 1, 'ndocumento' => '31927930', 'telefono1' => '3173221348', 'telefono2' => '3128052348', 'eps' => 83, 'email' => 'elizitamadorado@gmail.com', 'email_verified_at' => '2022-03-18 09:59:33', 'password' => '$2y$10$F7oAb/pGIGVS0N115YZix.MjSEv3qSvwCqk.7H9iZQlV8k96J33ae', 'created_at' => '2022-03-18 09:59:20', 'updated_at' => '2022-03-18 09:59:33'],
            ['id' => 334, 'name' => 'Nayive', 'apellido1' => 'García', 'apellido2' => 'Mosquera', 'tdocumento' => 1, 'ndocumento' => '1111778226', 'telefono1' => '3136294392', 'telefono2' => '3174832897', 'eps' => 60, 'email' => 'tulipannana@gmail.com', 'email_verified_at' => '2022-03-18 13:51:40', 'password' => '$2y$10$UTwclieCOICKrqs/We8kcuHz2oDv97l7dL0sLrpY.QO290fu6y5WG', 'created_at' => '2022-03-18 13:50:36', 'updated_at' => '2024-04-01 09:43:21'],
            ['id' => 331, 'name' => 'Paola Andrea', 'apellido1' => 'Claros', 'apellido2' => 'Jaramillo', 'tdocumento' => 1, 'ndocumento' => '38612286', 'telefono1' => '3172842110', 'telefono2' => null, 'eps' => 84, 'email' => 'paolaandreaclarosjaramillo@gmail.com', 'email_verified_at' => '2022-03-18 10:20:23', 'password' => '$2y$10$ZgCR4Sx5KsqrQIRMm9ieYu.SLqY/Pu27kigPfJHH0MsRyOfxehyLi', 'created_at' => '2022-03-18 10:19:40', 'updated_at' => '2022-03-18 10:20:23'],
            ['id' => 332, 'name' => 'Karent', 'apellido1' => 'Valencia', 'apellido2' => 'Caicedo', 'tdocumento' => 1, 'ndocumento' => '1111791805', 'telefono1' => '3162443629', 'telefono2' => '3104573891', 'eps' => 83, 'email' => 'hilducha15@hotmail.com', 'email_verified_at' => '2022-03-18 11:23:36', 'password' => '$2y$10$gesks8xZuBSy5yLRDBF5NeyblloGzSqejGTNqW7KkoH2fi/Bbz6Sa', 'created_at' => '2022-03-18 11:19:06', 'updated_at' => '2022-03-18 11:23:36'],
            ['id' => 333, 'name' => 'ALEXANDRA', 'apellido1' => 'CORDOBA', 'apellido2' => 'GRAJALES', 'tdocumento' => 1, 'ndocumento' => '31096261', 'telefono1' => '3217998071', 'telefono2' => '2293621', 'eps' => 134, 'email' => 'alexacorgra@outlook.com', 'email_verified_at' => '2022-03-18 12:00:27', 'password' => '$2y$10$Xa2tyfxyK41AoNVX9UzydOUO5UjNLf4VbRBJLYm6R8Rkir2.h5he6', 'created_at' => '2022-03-18 12:00:02', 'updated_at' => '2023-02-13 08:00:08'],
            ['id' => 355, 'name' => 'Joel', 'apellido1' => 'Arara', 'apellido2' => 'Diaz', 'tdocumento' => 1, 'ndocumento' => '14446905', 'telefono1' => '3137882018', 'telefono2' => '3172844553', 'eps' => 83, 'email' => 'mari14093@gmail.com', 'email_verified_at' => '2022-03-22 14:47:19', 'password' => '$2y$10$xMCzPgDmHqx1G/CQlJYBte773jWMtglieSmuqjbpW/Jeb5Z/FnEJ.', 'created_at' => '2022-03-22 14:46:12', 'updated_at' => '2023-06-20 15:15:22'],
            ['id' => 335, 'name' => 'Paula andrea', 'apellido1' => 'Kiriakides', 'apellido2' => 'Mejia', 'tdocumento' => 1, 'ndocumento' => '37393089', 'telefono1' => '3117622199', 'telefono2' => '3177354116', 'eps' => 83, 'email' => 'kiriakides1979@hotmail.com', 'email_verified_at' => '2022-03-18 19:41:39', 'password' => '$2y$10$axAoaKsNaw.RQYGt79WKwuQ2hiY6Dt2/HtBQHfvPW5U9oaCOe/QuS', 'created_at' => '2022-03-18 19:41:02', 'updated_at' => '2022-03-18 19:41:39'],
            ['id' => 352, 'name' => 'Carlos Alirio', 'apellido1' => 'Daza', 'apellido2' => 'Bedon', 'tdocumento' => 1, 'ndocumento' => '4625455', 'telefono1' => '3127886278', 'telefono2' => '3022156801', 'eps' => 83, 'email' => 'gloriadaza55@hotmail.com', 'email_verified_at' => '2022-03-22 14:21:51', 'password' => '$2y$10$CeuGP/iV1ETHj6kcERlxi.MsfUupsQ/5/ycKrmiWAFuU5m/9rEeNW', 'created_at' => '2022-03-22 14:20:38', 'updated_at' => '2022-06-03 18:17:50'],
            ['id' => 337, 'name' => 'maria viviana', 'apellido1' => 'torijano', 'apellido2' => 'triviño', 'tdocumento' => 1, 'ndocumento' => '31970441', 'telefono1' => '3116654729', 'telefono2' => null, 'eps' => 83, 'email' => 'mariavivianatorijano@gmail.com', 'email_verified_at' => '2022-03-19 10:50:29', 'password' => '$2y$10$eY7gt8QB7fafX8eLg1RFdezvAaKMSnsQsdV7xN9CBT/QC7UPGU1Ty', 'created_at' => '2022-03-19 10:50:15', 'updated_at' => '2022-03-19 10:50:29'],
            ['id' => 338, 'name' => 'Salvador', 'apellido1' => 'Camacho', 'apellido2' => 'García', 'tdocumento' => 4, 'ndocumento' => '1114628289', 'telefono1' => '3146190932', 'telefono2' => '3128656363', 'eps' => 84, 'email' => 'salvador190820@gmail.com', 'email_verified_at' => '2022-03-19 11:38:47', 'password' => '$2y$10$uuslM2Cm0VfgGBdbgXqA3.3i2lsmUDDFmG2DQGzY1qJ2EZqnb3a6G', 'created_at' => '2022-03-19 11:38:19', 'updated_at' => '2022-03-19 11:38:47'],
            ['id' => 339, 'name' => 'AMPARO', 'apellido1' => 'RIVERA', 'apellido2' => null, 'tdocumento' => 1, 'ndocumento' => '31903374', 'telefono1' => '3128126155', 'telefono2' => '3166821680', 'eps' => 83, 'email' => 'greymendezrivera@gmail.com', 'email_verified_at' => '2022-03-19 15:15:07', 'password' => '$2y$10$6iPx59JVPfk.sCnkJns.oOOkPFkEUtkjr1jMJDzSB0tg8t3uV/XCG', 'created_at' => '2022-03-19 15:14:28', 'updated_at' => '2022-03-19 15:15:07'],
            ['id' => 340, 'name' => 'Marleny', 'apellido1' => 'Camacho', 'apellido2' => 'Angulo', 'tdocumento' => 1, 'ndocumento' => '31380948', 'telefono1' => '3186175403', 'telefono2' => '3167459113', 'eps' => 83, 'email' => 'nury.rc@hotmail.com', 'email_verified_at' => '2022-03-20 09:28:30', 'password' => '$2y$10$afjUGh66izr6U24vvOb/ruTu.XXK8pP9TbVtGhcFpPJNF2o9lvZe6', 'created_at' => '2022-03-20 09:26:49', 'updated_at' => '2022-03-20 09:28:30'],
            ['id' => 336, 'name' => 'ERIKA MARCELA', 'apellido1' => 'CUERO', 'apellido2' => 'MANYOMA', 'tdocumento' => 1, 'ndocumento' => '1111813401', 'telefono1' => '3174791216', 'telefono2' => null, 'eps' => 83, 'email' => 'erikamarcelacuero@gmail.com', 'email_verified_at' => '2022-03-19 10:28:33', 'password' => '$2y$10$Uf9Kp./0bEQ5KB8f5ylUWOpqA8Iz5NErZBA7Weg3OQ7HKr0u/8.mm', 'created_at' => '2022-03-19 10:28:23', 'updated_at' => '2022-04-27 10:31:05'],
            ['id' => 342, 'name' => 'YOLANDA ELENA', 'apellido1' => 'BECERRA', 'apellido2' => 'BOLIVAR', 'tdocumento' => 1, 'ndocumento' => '31420385', 'telefono1' => '3117555572', 'telefono2' => '3105146721', 'eps' => 60, 'email' => 'jairogranada1960@gmail.com', 'email_verified_at' => '2022-03-21 16:19:34', 'password' => '$2y$10$pzL9G.KUppYlbo9e2wrcD.4I9S.Mf.NpE/voMkWnl2bXimzPSLkWG', 'created_at' => '2022-03-21 16:17:17', 'updated_at' => '2022-03-21 16:19:34'],
            ['id' => 216, 'name' => 'Lesly Yasmín', 'apellido1' => 'Pereira', 'apellido2' => 'Tezna', 'tdocumento' => 1, 'ndocumento' => '1118256974', 'telefono1' => '3217589259', 'telefono2' => '3106844757', 'eps' => 10, 'email' => 'lesly.pereira07@gmail.com', 'email_verified_at' => '2022-03-11 03:49:18', 'password' => '$2y$10$YprXPMo2i.GthzmJ2caliuJYkTfXjL7HpzbEYT3ueVifFs27JyUdW', 'created_at' => '2022-03-11 03:49:04', 'updated_at' => '2022-03-13 00:41:10'],
        ];

        foreach ($usuarios as $userData) {
            $user = User::updateOrCreate(['id' => $userData['id']], $userData);
            if (!$user->hasRole('Usuario')) {
                $user->assignRole('Usuario');
            }
        }

        $this->command->info('Lote 13: ' . count($usuarios) . ' usuarios insertados.');
    }
}
