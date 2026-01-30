<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignAdminRoleSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('ndocumento', '94529371')->first();

        if ($user) {
            if (!$user->hasRole('Administrador')) {
                $user->assignRole('Administrador');
                $this->command->info("Rol 'Administrador' asignado al usuario: {$user->name} (ndocumento: {$user->ndocumento})");
            } else {
                $this->command->info("El usuario ya tiene el rol 'Administrador'.");
            }
        } else {
            $this->command->error("No se encontró usuario con ndocumento = 94529371");
        }
    }
}
