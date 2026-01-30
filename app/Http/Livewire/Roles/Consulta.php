<?php

namespace App\Http\Livewire\Roles;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class Consulta extends Component
{
    use AuthorizesRequests;
    use WithPagination;
    
    public $filtroRol = '';
    public $esSuperAdmin = false;

    protected $queryString = ['filtroRol'];

    public function updatedFiltroRol()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->authorize('admin.roles.consultar');
        $this->esSuperAdmin = auth()->user()->hasRole('Super Admin');
    }

    public function render()
    {
        // Optimización: Usar JOIN en lugar de whereHas para mejor rendimiento
        $query = User::select(['users.id', 'users.name', 'users.apellido1', 'users.apellido2', 'users.ndocumento', 'roles.name as rol_nombre'])
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User');
        
        // Si NO es Super Admin, excluir usuarios con rol Super Admin
        if (!$this->esSuperAdmin) {
            $query->where('roles.name', '!=', 'Super Admin');
        }
        
        // Filtrar por rol si se seleccionó uno
        if ($this->filtroRol !== '') {
            $query->where('roles.name', $this->filtroRol);
        }
        
        $usuarios = $query->orderBy('users.name', 'asc')->paginate(10);
        
        // Obtener roles (excluir Super Admin si el usuario no es Super Admin)
        $rolesQuery = Role::orderBy('name', 'asc');
        if (!$this->esSuperAdmin) {
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        $roles = $rolesQuery->get(['id', 'name']);

        return view('livewire.roles.consulta', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'esSuperAdmin' => $this->esSuperAdmin,
        ]);
    }
}
