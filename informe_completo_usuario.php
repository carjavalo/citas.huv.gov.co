<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║     INFORME COMPLETO DE PRIVILEGIOS Y ACCESOS DE USUARIO          ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

// Buscar el usuario
$usuario = User::where('email', 'sebias908@gmail.com')->first();

if (!$usuario) {
    echo "❌ Usuario no encontrado.\n";
    echo "Nota: Verificar que el email sea exactamente 'sebias908@gmail.com'\n";
    exit;
}

echo "┌─ INFORMACIÓN DEL USUARIO ─────────────────────────────────────────┐\n";
echo "│ Nombre:     {$usuario->name} {$usuario->apellido1} {$usuario->apellido2}\n";
echo "│ Email:      {$usuario->email}\n";
echo "│ Documento:  {$usuario->ndocumento}\n";
echo "│ ID:         {$usuario->id}\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

// Roles
$roles = $usuario->roles;
echo "┌─ ROLES ASIGNADOS ──────────────────────────────────────────────────┐\n";
if ($roles->count() > 0) {
    foreach ($roles as $rol) {
        echo "│ ✓ {$rol->name}\n";
    }
} else {
    echo "│ ✗ Sin roles asignados\n";
}
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

// Permisos
$todosLosPermisos = $usuario->getAllPermissions();
echo "┌─ PERMISOS DEL SISTEMA ─────────────────────────────────────────────┐\n";
if ($todosLosPermisos->count() > 0) {
    foreach ($todosLosPermisos as $permiso) {
        echo "│ ✓ {$permiso->name}\n";
    }
} else {
    echo "│ ✗ Sin permisos específicos asignados\n";
}
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

// Vistas y accesos disponibles para rol "Usuario"
echo "┌─ VISTAS Y ACCESOS DISPONIBLES (ROL USUARIO) ──────────────────────┐\n";
echo "│\n";
echo "│ MENÚ PRINCIPAL:\n";
echo "│ ✓ Dashboard (Inicio)\n";
echo "│\n";
echo "│ MENÚ CITAS:\n";
echo "│ ✓ Solicitar Cita\n";
echo "│ ✓ Mis Solicitudes (ver solo sus propias citas)\n";
echo "│\n";
echo "│ MENÚ PERFIL:\n";
echo "│ ✓ Ver Perfil\n";
echo "│ ✓ Editar Perfil\n";
echo "│ ✓ Cambiar Contraseña\n";
echo "│ ✓ Cerrar Sesión\n";
echo "│\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ VISTAS RESTRINGIDAS (NO DISPONIBLES) ────────────────────────────┐\n";
echo "│\n";
echo "│ ✗ Consultar Solicitudes (todas las citas)\n";
echo "│ ✗ Consultar Usuarios\n";
echo "│ ✗ Registrar Usuarios\n";
echo "│ ✗ Configuración (EPS, Servicios, Sedes, etc.)\n";
echo "│ ✗ Reportes\n";
echo "│ ✗ Gestión de Roles\n";
echo "│ ✗ Campañas\n";
echo "│ ✗ Operando SQL\n";
echo "│\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

// Verificar si tiene accesos adicionales
$tieneAccesosAdicionales = false;

// Verificar roles adicionales
if ($roles->count() > 1) {
    $tieneAccesosAdicionales = true;
}

// Verificar permisos directos
$permisosDirectos = $usuario->permissions;
if ($permisosDirectos->count() > 0) {
    $tieneAccesosAdicionales = true;
}

// Verificar si el rol es diferente a Usuario
$esRolUsuario = $roles->contains('name', 'Usuario');
if (!$esRolUsuario) {
    $tieneAccesosAdicionales = true;
}

echo "┌─ ANÁLISIS DE SEGURIDAD ────────────────────────────────────────────┐\n";
echo "│\n";

if (!$tieneAccesosAdicionales && $esRolUsuario && $roles->count() === 1) {
    echo "│ ✅ CONFIGURACIÓN CORRECTA\n";
    echo "│\n";
    echo "│ El usuario tiene EXACTAMENTE los privilegios de un usuario\n";
    echo "│ con rol 'Usuario':\n";
    echo "│\n";
    echo "│ • Solo 1 rol asignado: Usuario\n";
    echo "│ • Sin permisos directos adicionales\n";
    echo "│ • Sin roles adicionales\n";
    echo "│ • Acceso limitado a sus propias citas\n";
    echo "│ • No puede acceder a configuración ni administración\n";
    echo "│\n";
} else {
    echo "│ ⚠️  REQUIERE CORRECCIÓN\n";
    echo "│\n";
    
    if ($roles->count() > 1) {
        echo "│ ⚠️  Tiene múltiples roles:\n";
        foreach ($roles as $rol) {
            echo "│    - {$rol->name}\n";
        }
        echo "│\n";
    }
    
    if (!$esRolUsuario) {
        echo "│ ⚠️  No tiene el rol 'Usuario'\n";
        echo "│\n";
    }
    
    if ($permisosDirectos->count() > 0) {
        echo "│ ⚠️  Tiene permisos directos adicionales:\n";
        foreach ($permisosDirectos as $permiso) {
            echo "│    - {$permiso->name}\n";
        }
        echo "│\n";
    }
}

echo "└────────────────────────────────────────────────────────────────────┘\n\n";

// Comparación con otros roles
echo "┌─ COMPARACIÓN CON OTROS ROLES ──────────────────────────────────────┐\n";
echo "│\n";

$todosLosRoles = Role::all();
foreach ($todosLosRoles as $rol) {
    $permisos = $rol->permissions;
    echo "│ ROL: {$rol->name}\n";
    echo "│ Permisos: " . $permisos->count() . "\n";
    
    if ($permisos->count() > 0 && $permisos->count() <= 5) {
        foreach ($permisos as $permiso) {
            echo "│   - {$permiso->name}\n";
        }
    } elseif ($permisos->count() > 5) {
        echo "│   (Múltiples permisos de administración)\n";
    } else {
        echo "│   (Sin permisos específicos)\n";
    }
    echo "│\n";
}

echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ CONCLUSIÓN ───────────────────────────────────────────────────────┐\n";
echo "│\n";

if (!$tieneAccesosAdicionales && $esRolUsuario && $roles->count() === 1) {
    echo "│ ✅ El usuario 'sebias9088@gmail.com' está configurado\n";
    echo "│    correctamente con los privilegios de un usuario estándar.\n";
    echo "│\n";
    echo "│ ✅ No tiene accesos adicionales ni privilegios elevados.\n";
    echo "│\n";
    echo "│ ✅ Solo puede:\n";
    echo "│    • Ver el dashboard\n";
    echo "│    • Solicitar citas\n";
    echo "│    • Ver sus propias citas\n";
    echo "│    • Gestionar su perfil\n";
    echo "│\n";
} else {
    echo "│ ⚠️  ACCIÓN REQUERIDA:\n";
    echo "│\n";
    echo "│ Se recomienda:\n";
    echo "│ 1. Remover todos los roles actuales\n";
    echo "│ 2. Asignar únicamente el rol 'Usuario'\n";
    echo "│ 3. Verificar que no tenga permisos directos\n";
    echo "│\n";
}

echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "Fecha del informe: " . date('d/m/Y H:i:s') . "\n";
echo "Generado por: Sistema de Auditoría\n\n";
