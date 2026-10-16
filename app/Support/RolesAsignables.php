<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Roles que un usuario puede asignar al crear o editar usuarios, según lo
 * configurado en el Gestor de permisos (tabla role_roles_asignables).
 */
class RolesAsignables
{
    /** Roles que el usuario puede asignar (id, name). */
    public static function paraUsuario(User $user)
    {
        // Super Admin asigna cualquier rol, incluido Super Admin.
        if ($user->hasRole(CatalogoPermisos::SUPER_ADMIN)) {
            return Role::orderBy('name')->get(['id', 'name']);
        }

        $ids = DB::table('role_roles_asignables')
            ->whereIn('role_id', $user->roles()->pluck('id'))
            ->pluck('rol_asignable_id');

        // Solo Super Admin entrega Super Admin, aunque quedara una fila suelta.
        return Role::whereIn('id', $ids)
            ->where('name', '!=', CatalogoPermisos::SUPER_ADMIN)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /** @return list<int> */
    public static function idsDelRol(int $roleId): array
    {
        return DB::table('role_roles_asignables')
            ->where('role_id', $roleId)
            ->pluck('rol_asignable_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /** Reemplaza el conjunto de roles asignables de un rol. */
    public static function guardar(int $roleId, array $ids): void
    {
        DB::transaction(function () use ($roleId, $ids) {
            DB::table('role_roles_asignables')->where('role_id', $roleId)->delete();
            foreach (array_unique(array_map('intval', $ids)) as $id) {
                DB::table('role_roles_asignables')->insert([
                    'role_id' => $roleId,
                    'rol_asignable_id' => $id,
                ]);
            }
        });
    }
}
