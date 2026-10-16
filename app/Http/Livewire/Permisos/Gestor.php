<?php

namespace App\Http\Livewire\Permisos;

use App\Support\CatalogoPermisos;
use App\Support\RolesAsignables;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Gestor de Permisos: matriz de vistas y acciones por rol. Solo Super Admin.
 *
 * La matriz se guarda en los permisos de Spatie del rol, así que lo que el rol
 * ya tenía aparece marcado al abrirla y todo el sistema la respeta sin cambios.
 */
class Gestor extends Component
{
    public $roleId = null;

    /** @var array<string, bool> permiso => asignado */
    public $marcados = [];

    /** @var list<string> ids de los roles que el rol puede asignar a usuarios */
    public $asignables = [];

    public function mount()
    {
        $this->soloSuperAdmin();

        // ?role=ID: llega desde Gestión Roles con el rol ya elegido.
        $roles = $this->roles();
        $pedido = $roles->firstWhere('id', (int) request()->query('role'));
        $this->seleccionarRol(($pedido ?? $roles->first())->id ?? null);
    }

    public function seleccionarRol($roleId)
    {
        $this->soloSuperAdmin();

        $rol = $roleId ? $this->roles()->firstWhere('id', (int) $roleId) : null;
        $this->roleId = $rol->id ?? null;

        $asignados = $rol ? $rol->permissions()->pluck('name')->all() : [];
        $this->asignables = $rol
            ? array_map('strval', RolesAsignables::idsDelRol($rol->id))
            : [];

        $this->marcados = [];
        foreach (CatalogoPermisos::todosLosPermisos() as $permiso) {
            $this->marcados[$this->clave($permiso)] = in_array($permiso, $asignados, true);
        }
    }

    /** Marca o desmarca todas las acciones de una columna (ver, crear...). */
    public function alternarColumna($accion)
    {
        $permisos = [];
        foreach (CatalogoPermisos::VISTAS as $vista) {
            if (isset($vista['acciones'][$accion])) {
                $permisos[] = $vista['acciones'][$accion];
            }
        }
        $this->alternar($permisos);
    }

    /** Marca o desmarca todas las acciones de una vista. */
    public function alternarVista($key)
    {
        foreach (CatalogoPermisos::VISTAS as $vista) {
            if ($vista['key'] === $key) {
                $this->alternar(CatalogoPermisos::permisosDe($vista));
            }
        }
    }

    public function marcarTodo()
    {
        foreach ($this->marcados as $k => $v) {
            $this->marcados[$k] = true;
        }
    }

    public function limpiarTodo()
    {
        foreach ($this->marcados as $k => $v) {
            $this->marcados[$k] = false;
        }
    }

    public function guardar()
    {
        $this->soloSuperAdmin();

        // Modelo completo: Spatie necesita guard_name para asignar permisos.
        $rol = Role::where('name', '!=', CatalogoPermisos::SUPER_ADMIN)->find((int) $this->roleId);
        if (!$rol) {
            $this->emit('alertError', 'Seleccione un rol válido.');
            return;
        }

        $catalogo = CatalogoPermisos::todosLosPermisos();
        $existentes = Permission::where('guard_name', 'web')->whereIn('name', $catalogo)->pluck('name')->all();

        $faltantes = array_diff($catalogo, $existentes);
        if ($faltantes) {
            $this->emit('alertError', 'Faltan permisos en la base de datos ('.implode(', ', $faltantes).'). Ejecute php artisan migrate.');
            return;
        }

        // Sin «Ver» la vista no se abre: sus demás acciones se apagan para que
        // no queden asignadas a escondidas.
        foreach (CatalogoPermisos::VISTAS as $vista) {
            if (isset($vista['acciones']['ver']) && empty($this->marcados[$this->clave($vista['acciones']['ver'])])) {
                foreach (CatalogoPermisos::permisosDe($vista) as $permiso) {
                    $this->marcados[$this->clave($permiso)] = false;
                }
            }
        }

        $dar = [];
        $quitar = [];
        foreach ($catalogo as $permiso) {
            if (!empty($this->marcados[$this->clave($permiso)])) {
                $dar[] = $permiso;
            } else {
                $quitar[] = $permiso;
            }
        }

        // Solo se tocan los permisos del catálogo: si el rol tiene otros
        // (creados a mano o por otro módulo), se respetan.
        $rol->givePermissionTo($dar);
        foreach ($quitar as $permiso) {
            if ($rol->hasPermissionTo($permiso)) {
                $rol->revokePermissionTo($permiso);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Super Admin no se ofrece como asignable: solo él puede entregarlo.
        $candidatos = $this->rolesAsignablesCandidatos()->pluck('id')->all();
        RolesAsignables::guardar($rol->id, array_intersect(array_map('intval', (array) $this->asignables), $candidatos));

        $this->emit('alertSuccess', "Permisos del rol {$rol->name} guardados correctamente.");
    }

    public function render()
    {
        $roles = $this->roles();
        $rolActivo = $roles->firstWhere('id', (int) $this->roleId);

        $totalAcciones = count(CatalogoPermisos::todosLosPermisos());
        $activas = count(array_filter($this->marcados));

        return view('livewire.permisos.gestor', [
            'roles' => $roles,
            'rolActivo' => $rolActivo,
            'vistas' => CatalogoPermisos::VISTAS,
            'grupos' => CatalogoPermisos::grupos(),
            'acciones' => CatalogoPermisos::ACCIONES,
            'totalVistas' => count(CatalogoPermisos::VISTAS),
            'totalAcciones' => $totalAcciones,
            'activas' => $activas,
            'candidatosAsignables' => $this->rolesAsignablesCandidatos(),
        ]);
    }

    /**
     * Livewire no admite puntos en las claves de un array enlazado con
     * wire:model (los toma como anidación), así que se reemplazan.
     */
    public function clave(string $permiso): string
    {
        return str_replace('.', '__', $permiso);
    }

    private function alternar(array $permisos): void
    {
        $claves = array_map([$this, 'clave'], $permisos);
        $todos = collect($claves)->every(fn ($k) => !empty($this->marcados[$k]));
        foreach ($claves as $k) {
            $this->marcados[$k] = !$todos;
        }
    }

    private function rolesAsignablesCandidatos()
    {
        return Role::where('name', '!=', CatalogoPermisos::SUPER_ADMIN)->orderBy('name')->get(['id', 'name']);
    }

    private function roles()
    {
        return Role::where('name', '!=', CatalogoPermisos::SUPER_ADMIN)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * El gestor no es asignable: quien lo use podría darse cualquier permiso.
     * Los métodos públicos de Livewire se invocan desde el navegador, así que
     * la verificación se repite en cada acción que escribe.
     */
    private function soloSuperAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasRole(CatalogoPermisos::SUPER_ADMIN), 403);
    }
}
