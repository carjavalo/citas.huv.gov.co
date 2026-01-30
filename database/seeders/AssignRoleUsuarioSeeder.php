<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignRoleUsuarioSeeder extends Seeder
{
    public function run()
    {
        $count = 0;
        $total = 0;

        User::chunk(500, function ($users) use (&$count, &$total) {
            foreach ($users as $user) {
                $total++;
                if (!$user->hasRole('Usuario')) {
                    $user->assignRole('Usuario');
                    $count++;
                }
            }
        });

        $this->command->info("Rol 'Usuario' asignado a {$count} usuarios nuevos.");
        $this->command->info("Total usuarios procesados: {$total}");
    }
}
