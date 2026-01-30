<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Actualizando usuarios...\n";

$count = User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);

echo "Se han verificado $count usuarios existentes.\n";
