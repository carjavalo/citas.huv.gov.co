<?php

namespace App\Http\Livewire\Roles;

use App\Support\CatalogoPermisos;
use App\Support\RolesAsignables;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Gestión Roles: CRUD de los roles del sistema. Los permisos de cada rol se
 * configuran luego en el Gestor de permisos.
 */
class Gestion extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public $busqueda = '';

    public $modal = false;
    public $rolId = null;
    public $nombre = '';
    public $descripcion = '';
    public $requiere_sede = false;
    /** Al crear: rol del que se copian permisos y roles asignables. */
    public $copiar_de = '';
    /** ids (string) de los roles que pueden asignar este rol a usuarios. */
    public $asignadoPor = [];

    protected $queryString = ['busqueda' => ['except' => '']];

    public function mount()
    {
        $this->authorize('admin.roles.gestionar');
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function crear()
    {
        $this->authorize('admin.roles.crear');
        $this->limpiar();
        $this->modal = true;
    }

    public function editar($id)
    {
        $this->authorize('admin.roles.editar');
        $rol = Role::findOrFail($id);
        $this->abortarSiSuperAdmin($rol);

        $this->limpiar();
        $this->rolId = $rol->id;
        $this->nombre = $rol->name;
        $this->descripcion = $rol->descripcion;
        $this->requiere_sede = (bool) $rol->requiere_sede;
        $this->asignadoPor = DB::table('role_roles_asignables')
            ->where('rol_asignable_id', $rol->id)
            ->pluck('role_id')->map(fn ($id) => (string) $id)->all();
        $this->modal = true;
    }

    public function cerrar()
    {
        $this->limpiar();
        $this->modal = false;
    }

    public function guardar()
    {
        $this->authorize($this->rolId ? 'admin.roles.editar' : 'admin.roles.crear');

        $rol = $this->rolId ? Role::findOrFail($this->rolId) : null;
        if ($rol) {
            $this->abortarSiSuperAdmin($rol);
        }

        $this->nombre = trim(preg_replace('/\s+/', ' ', (string) $this->nombre));

        $this->validate([
            'nombre' => [
                'required', 'string', 'min:3', 'max:100',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($this->rolId),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'requiere_sede' => ['boolean'],
            'copiar_de' => ['nullable', 'integer', 'exists:roles,id'],
            'asignadoPor' => ['array'],
            'asignadoPor.*' => ['integer', 'exists:roles,id'],
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.unique' => 'Ya existe un rol con ese nombre.',
        ]);

        if ($rol && $this->esSistema($rol) && $rol->name !== $this->nombre) {
            $this->addError('nombre', 'Este es un rol del sistema: su nombre no se puede cambiar.');
            return;
        }

        DB::transaction(function () use (&$rol) {
            $datos = [
                'name' => $this->nombre,
                'descripcion' => $this->descripcion ?: null,
                'requiere_sede' => (bool) $this->requiere_sede,
            ];

            if ($rol) {
                $rol->update($datos);
            } else {
                $rol = Role::create($datos + ['guard_name' => 'web']);

                if ($this->copiar_de) {
                    $origen = Role::find($this->copiar_de);
                    $rol->syncPermissions($origen->permissions);
                    RolesAsignables::guardar($rol->id, array_diff(RolesAsignables::idsDelRol($origen->id), [$rol->id]));
                }
            }

            // Quién puede asignar este rol. Super Admin siempre puede.
            $permitidos = Role::where('name', '!=', CatalogoPermisos::SUPER_ADMIN)->pluck('id')->all();
            $quienes = array_intersect(array_map('intval', (array) $this->asignadoPor), $permitidos);
            DB::table('role_roles_asignables')->where('rol_asignable_id', $rol->id)->delete();
            foreach ($quienes as $roleId) {
                DB::table('role_roles_asignables')->insert(['role_id' => $roleId, 'rol_asignable_id' => $rol->id]);
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $nuevo = !$this->rolId;
        $this->cerrar();
        $this->emit('alertSuccess', $nuevo
            ? "Rol {$rol->name} creado. Configure sus permisos en el Gestor de permisos."
            : "Rol {$rol->name} actualizado.");
    }

    public function eliminar($id)
    {
        $this->authorize('admin.roles.eliminar');
        $rol = Role::findOrFail($id);

        if ($this->esSistema($rol)) {
            $this->emit('alertError', "El rol {$rol->name} es del sistema y no se puede eliminar.");
            return;
        }

        $usuarios = $this->usuariosDe($rol->id);
        if ($usuarios > 0) {
            $this->emit('alertError', "El rol {$rol->name} tiene {$usuarios} usuario(s) asignado(s). Cámbieles el rol antes de eliminarlo.");
            return;
        }

        $nombre = $rol->name;
        // Spatie borra sus permisos; role_roles_asignables cae en cascada.
        $rol->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->emit('alertSuccess', "Rol {$nombre} eliminado.");
    }

    public function render()
    {
        $roles = Role::query()
            ->select('roles.*')
            ->selectSub(
                DB::table('model_has_roles')->selectRaw('count(*)')->whereColumn('model_has_roles.role_id', 'roles.id'),
                'usuarios_count'
            )
            ->selectSub(
                DB::table('role_has_permissions')->selectRaw('count(*)')->whereColumn('role_has_permissions.role_id', 'roles.id'),
                'permisos_count'
            )
            ->when($this->busqueda !== '', fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->busqueda.'%')
                  ->orWhere('descripcion', 'like', '%'.$this->busqueda.'%');
            }))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.roles.gestion', [
            'roles' => $roles,
            'rolesSistema' => CatalogoPermisos::ROLES_SISTEMA,
            'otrosRoles' => Role::where('name', '!=', CatalogoPermisos::SUPER_ADMIN)
                ->when($this->rolId, fn ($q) => $q->where('id', '!=', $this->rolId))
                ->orderBy('name')->get(['id', 'name']),
            // Incluye al propio rol: un rol puede asignarse a sí mismo (p. ej. Coordinador).
            'asignadores' => Role::where('name', '!=', CatalogoPermisos::SUPER_ADMIN)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function limpiar(): void
    {
        $this->resetErrorBag();
        $this->rolId = null;
        $this->nombre = '';
        $this->descripcion = '';
        $this->requiere_sede = false;
        $this->copiar_de = '';
        $this->asignadoPor = [];
    }

    private function esSistema(Role $rol): bool
    {
        return in_array($rol->name, CatalogoPermisos::ROLES_SISTEMA, true);
    }

    private function usuariosDe(int $roleId): int
    {
        return DB::table('model_has_roles')->where('role_id', $roleId)->count();
    }

    /** Super Admin no se edita desde aquí: su acceso total no depende del rol. */
    private function abortarSiSuperAdmin(Role $rol): void
    {
        abort_if($rol->name === CatalogoPermisos::SUPER_ADMIN, 403);
    }
}
