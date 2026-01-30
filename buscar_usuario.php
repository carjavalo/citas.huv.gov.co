<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Buscando usuarios con email similar a 'sebias'...\n\n";

$usuarios = User::where('email', 'like', '%sebias%')->get();

if ($usuarios->count() > 0) {
    foreach ($usuarios as $user) {
        echo "ID: {$user->id}\n";
        echo "Nombre: {$user->name} {$user->apellido1}\n";
        echo "Email: {$user->email}\n";
        echo "Documento: {$user->ndocumento}\n";
        $roles = $user->roles->pluck('name')->join(', ');
        echo "Roles: {$roles}\n";
        echo str_repeat("-", 50) . "\n";
    }
} else {
    echo "No se encontraron usuarios.\n";
}
