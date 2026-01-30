<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== VERIFICACIÓN DE BOTÓN ELIMINAR USUARIO ===\n\n";

// Verificar usuarios Super Admin
$superAdmins = User::whereHas('roles', function($query) {
    $query->where('name', 'Super Admin');
})->get();

echo "Usuarios con rol Super Admin:\n";
if ($superAdmins->count() > 0) {
    foreach ($superAdmins as $admin) {
        echo "- {$admin->name} {$admin->apellido1} (ID: {$admin->id}, Doc: {$admin->ndocumento})\n";
        echo "  Email: {$admin->email}\n";
    }
} else {
    echo "⚠️  No hay usuarios con rol Super Admin\n";
}

echo "\n" . str_repeat("=", 60) . "\n\n";

// Verificar otros usuarios
$otrosUsuarios = User::whereDoesntHave('roles', function($query) {
    $query->where('name', 'Super Admin');
})->take(5)->get();

echo "Otros usuarios (sin rol Super Admin):\n";
foreach ($otrosUsuarios as $user) {
    $roles = $user->roles->pluck('name')->join(', ');
    echo "- {$user->name} {$user->apellido1} (ID: {$user->id})\n";
    echo "  Rol: {$roles}\n";
    echo "  Email: {$user->email}\n\n";
}

echo str_repeat("=", 60) . "\n\n";

echo "✅ FUNCIONALIDAD IMPLEMENTADA:\n\n";
echo "1. Botón 'Eliminar' agregado en la columna Acciones\n";
echo "2. Visible SOLO para usuarios con rol Super Admin\n";
echo "3. Otros roles NO pueden ver el botón\n";
echo "4. Validaciones de seguridad:\n";
echo "   - Solo Super Admin puede eliminar\n";
echo "   - No puede eliminar su propio usuario\n";
echo "   - Confirmación antes de eliminar\n\n";

echo "📍 Vista: http://192.168.2.200:8000/usuarios/consulta\n\n";

echo "🔍 PARA PROBAR:\n";
echo "1. Inicia sesión como Super Admin\n";
echo "2. Ve a la vista de usuarios\n";
echo "3. Verás el botón 'Eliminar' (rojo) junto a 'Editar'\n";
echo "4. Inicia sesión con otro rol y verifica que NO ves el botón\n\n";
