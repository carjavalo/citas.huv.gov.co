<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AsignarRolUsuarioSinRolSeeder extends Seeder
{
    /**
     * Asigna el rol "Usuario" a todos los usuarios que no tienen ningún rol asignado.
     */
    public function run()
    {
        // Verificar que el rol "Usuario" existe
        $rolUsuario = Role::where('name', 'Usuario')->first();
        
        if (!$rolUsuario) {
            $this->command->error('El rol "Usuario" no existe en la base de datos.');
            return;
        }

        // Obtener usuarios sin ningún rol asignado
        $usuariosSinRol = User::doesntHave('roles')->get();
        
        $contador = 0;
        foreach ($usuariosSinRol as $usuario) {
            $usuario->assignRole('Usuario');
            $contador++;
        }

        $this->command->info("Se asignó el rol 'Usuario' a {$contador} usuarios.");
    }
}
