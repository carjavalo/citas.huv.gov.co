<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "=== INFORME DE PRIVILEGIOS DE USUARIO ===\n\n";

// Buscar el usuario
$usuario = User::where('email', 'sebias9088@gmail.com')->first();

if (!$usuario) {
    echo "❌ Usuario con email 'sebias9088@gmail.com' no encontrado.\n";
    echo "Nota: El email en la base de datos es 'sebias908@gmail.com' (sin la segunda '8')\n\n";
    
    // Buscar con el email correcto
    $usuario = User::where('email', 'sebias908@gmail.com')->first();
    
    if (!$usuario) {
        echo "❌ Usuario no encontrado con ninguna variación del email.\n";
        exit;
    }
}

echo "✅ USUARIO ENCONTRADO:\n";
echo str_repeat("=", 70) . "\n";
echo "Nombre: {$usuario->name} {$usuario->apellido1} {$usuario->apellido2}\n";
echo "Email: {$usuario->email}\n";
echo "Documento: {$usuario->ndocumento}\n";
echo "ID: {$usuario->id}\n";
echo str_repeat("=", 70) . "\n\n";

// Obtener roles del usuario
$roles = $usuario->roles;
echo "ROLES ASIGNADOS:\n";
echo str_repeat("-", 70) . "\n";
if ($roles->count() > 0) {
    foreach ($roles as $rol) {
        echo "- {$rol->name}\n";
    }
} else {
    echo "- Sin roles asignados\n";
}
echo "\n";

// Obtener permisos directos del usuario
$permisosDirectos = $usuario->permissions;
echo "PERMISOS DIRECTOS (asignados al usuario):\n";
echo str_repeat("-", 70) . "\n";
if ($permisosDirectos->count() > 0) {
    foreach ($permisosDirectos as $permiso) {
        echo "- {$permiso->name}\n";
    }
} else {
    echo "- Sin permisos directos\n";
}
echo "\n";

// Obtener todos los permisos (incluyendo los del rol)
$todosLosPermisos = $usuario->getAllPermissions();
echo "TODOS LOS PERMISOS (rol + directos):\n";
echo str_repeat("-", 70) . "\n";
if ($todosLosPermisos->count() > 0) {
    foreach ($todosLosPermisos as $permiso) {
        echo "- {$permiso->name}\n";
    }
} else {
    echo "- Sin permisos\n";
}
echo "\n";

// Comparar con el rol "Usuario"
$rolUsuario = Role::where('name', 'Usuario')->first();
if ($rolUsuario) {
    echo "PERMISOS DEL ROL 'USUARIO' (referencia):\n";
    echo str_repeat("-", 70) . "\n";
    $permisosRolUsuario = $rolUsuario->permissions;
    if ($permisosRolUsuario->count() > 0) {
        foreach ($permisosRolUsuario as $permiso) {
            echo "- {$permiso->name}\n";
        }
    } else {
        echo "- Sin permisos asignados al rol Usuario\n";
    }
    echo "\n";
}

// Análisis de diferencias
echo "ANÁLISIS:\n";
echo str_repeat("=", 70) . "\n";

$rolesActuales = $roles->pluck('name')->toArray();
$tieneRolUsuario = in_array('Usuario', $rolesActuales);

if ($tieneRolUsuario && $roles->count() === 1) {
    echo "✅ El usuario tiene SOLO el rol 'Usuario'\n";
} elseif ($tieneRolUsuario && $roles->count() > 1) {
    echo "⚠️  El usuario tiene el rol 'Usuario' pero TAMBIÉN tiene otros roles:\n";
    foreach ($rolesActuales as $rol) {
        if ($rol !== 'Usuario') {
            echo "   - {$rol}\n";
        }
    }
} else {
    echo "❌ El usuario NO tiene el rol 'Usuario'\n";
    echo "   Roles actuales: " . implode(', ', $rolesActuales) . "\n";
}

echo "\n";

if ($permisosDirectos->count() > 0) {
    echo "⚠️  El usuario tiene permisos DIRECTOS adicionales:\n";
    foreach ($permisosDirectos as $permiso) {
        echo "   - {$permiso->name}\n";
    }
    echo "\n";
}

// Verificar permisos extras
if ($rolUsuario) {
    $permisosRolUsuarioIds = $rolUsuario->permissions->pluck('id')->toArray();
    $todosLosPermisosIds = $todosLosPermisos->pluck('id')->toArray();
    $permisosExtras = array_diff($todosLosPermisosIds, $permisosRolUsuarioIds);
    
    if (count($permisosExtras) > 0) {
        echo "⚠️  PERMISOS ADICIONALES (no incluidos en rol Usuario):\n";
        foreach ($permisosExtras as $permisoId) {
            $permiso = Permission::find($permisoId);
            if ($permiso) {
                echo "   - {$permiso->name}\n";
            }
        }
        echo "\n";
    }
}

echo "\n";
echo "RECOMENDACIÓN:\n";
echo str_repeat("=", 70) . "\n";

if ($tieneRolUsuario && $roles->count() === 1 && $permisosDirectos->count() === 0) {
    echo "✅ El usuario está configurado correctamente.\n";
    echo "   Solo tiene el rol 'Usuario' sin permisos adicionales.\n";
} else {
    echo "⚠️  Se requiere corrección:\n";
    if ($roles->count() > 1) {
        echo "   1. Remover roles adicionales (mantener solo 'Usuario')\n";
    }
    if (!$tieneRolUsuario) {
        echo "   1. Asignar el rol 'Usuario'\n";
    }
    if ($permisosDirectos->count() > 0) {
        echo "   2. Remover permisos directos\n";
    }
}

echo "\n";
