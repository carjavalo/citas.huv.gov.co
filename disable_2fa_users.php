<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$usersWith2FA = User::whereNotNull('two_factor_secret')->count();

echo "Usuarios con 2FA habilitado: $usersWith2FA\n";

if ($usersWith2FA > 0) {
    echo "Deshabilitando 2FA para estos usuarios...\n";
    User::whereNotNull('two_factor_secret')->update([
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null
    ]);
    echo "2FA deshabilitado correctamente.\n";
} else {
    echo "No se encontraron usuarios con 2FA habilitado.\n";
}
