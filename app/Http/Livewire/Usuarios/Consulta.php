<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\eps;
use App\Models\tipo_identificacion;
use App\Models\User;
use App\Models\Sede;
use App\Models\Pservicio;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class Consulta extends Component
{
    use AuthorizesRequests;
    use WithPagination;
    
    public $modal = false; //Encargada de la modal
    public $usu_id, $nombres, $apellido1, $apellido2, $email, $usu_rol, $usu_eps, $tdoc, $ndoc; //Datos del usuario para editar
    public $aseguradoras; //Almacena las eps
    public $tipos_identificacion;
    public $apellidos1 = '';
    public $nombre= '';
    public $identificacion ='';
    
    // Propiedades para sede y pservicio (solo rol Consultor)
    public $sedes = [];
    public $pservicios = [];
    public $usu_sede;
    public $usu_pservicio;

    public function updatingIdentificacion()
    {
        $this->resetPage();
    }

    public function updatingNombre()
    {
        $this->resetPage();
    }

    public function updatingApellidos1()
    {
        $this->resetPage();
    } 

    public function mount()
    {
        $this->authorize('admin.usuarios.consult');
    }
    public function render() 
    {
        // Construir la consulta base de usuarios
        $query = User::select(['id','name','apellido1','apellido2','email','ndocumento'])
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->nombre) .'%'])
            ->whereRaw('LOWER(apellido1) LIKE ?', ['%' . strtolower($this->apellidos1) .'%'])
            ->where('ndocumento','like','%'.$this->identificacion.'%');
        
        // Si el usuario actual NO es Super Admin, excluir usuarios con rol Super Admin
        if (!auth()->user()->hasRole('Super Admin')) {
            $query->whereDoesntHave('roles', function($q) {
                $q->where('name', 'Super Admin');
            });
        }
        
        return view('livewire.usuarios.consulta', [
            'usuarios'  => $query->orderBy('name','asc')->paginate(10),           
            'roles'     => $this->getRolesVisibles(),
        ]);


    }










    public function abrirModal()
    {
        $this->modal = true;
    }

    public function cerrarModal()
    {
        $this->reset();
        $this->modal = false;
    }

    private function getRolesVisibles()
    {
        $user = auth()->user();
        
        // Super Admin ve todos los roles
        if ($user->hasRole('Super Admin')) {
            return Role::all(['id', 'name']);
        }
        
        // Administrador ve todos excepto Super Admin y Administrador
        if ($user->hasRole('Administrador')) {
            return Role::whereNotIn('name', ['Super Admin', 'Administrador'])->get(['id', 'name']);
        }
        
        // Coordinador no ve: Super Admin, Administrador, Gestor Obstetricia, Hospital
        if ($user->hasRole('Coordinador')) {
            return Role::whereNotIn('name', ['Super Admin', 'Administrador', 'Gestor Obstetricia', 'Hospital'])->get(['id', 'name']);
        }
        
        // Por defecto, no ve Super Admin ni Coordinador
        return Role::whereNotIn('name', ['Super Admin', 'Coordinador'])->get(['id', 'name']);
    }

    private function getRolesRestringidos()
    {
        $user = auth()->user();
        
        // Super Admin puede asignar cualquier rol
        if ($user->hasRole('Super Admin')) {
            return [];
        }
        
        // Administrador no puede asignar Super Admin ni Administrador
        if ($user->hasRole('Administrador')) {
            return ['Super Admin', 'Administrador'];
        }
        
        // Coordinador no puede asignar: Super Admin, Administrador, Gestor Obstetricia, Hospital
        if ($user->hasRole('Coordinador')) {
            return ['Super Admin', 'Administrador', 'Gestor Obstetricia', 'Hospital'];
        }
        
        // Por defecto, roles restringidos
        return ['Super Admin', 'Coordinador'];
    }

    public function editar($id) //Esta función inicializa variables para mostrar en la modal
    {
        $usuario                        = User::where('id','=',$id)->first(['id','name','apellido1','apellido2','email','eps','tdocumento','ndocumento','sede_id','pservicio_id']);
        if (!$usuario) {
            $this->emit('alertError', 'No se encontró el usuario.');
            return;
        }
        $this->usu_id                   = $usuario->id;
        $this->nombres                  = $usuario->name; 
        $this->apellido1                = $usuario->apellido1;
        $this->apellido2                = $usuario->apellido2;
        $this->email                    = $usuario->email; 
        $this->usu_rol                  = optional($usuario->roles->first())->name ?? 'Sin rol';
        $this->usu_eps                  = $usuario->eps;
        $this->aseguradoras             = eps::all();
        $this->tipos_identificacion     = tipo_identificacion::all();
        $this->tdoc                     = $usuario->tdocumento;
        $this->ndoc                     = $usuario->ndocumento;
        
        // Cargar sedes
        $this->sedes                    = Sede::where('estado', 1)->get();
        $this->usu_sede                 = $usuario->sede_id;
        $this->usu_pservicio            = $usuario->pservicio_id;
        
        // Cargar pservicios si hay sede seleccionada
        if ($this->usu_sede) {
            $this->pservicios = Pservicio::where('sede_id', $this->usu_sede)->get();
        }
        
        $this->abrirModal();
    }

    // Cuando cambia el rol, resetear sede y pservicio si no es Consultor, Coordinador o Administrador
    public function updatedUsuRol($value)
    {
        $rolesConSede = ['Consultor', 'Coordinador', 'Administrador'];
        if (in_array($value, $rolesConSede)) {
            // Cargar sedes cuando se selecciona rol Consultor, Coordinador o Administrador
            $this->sedes = Sede::where('estado', 1)->get();
        } else {
            $this->usu_sede = null;
            $this->usu_pservicio = null;
            $this->pservicios = [];
            $this->sedes = [];
        }
    }

    // Cuando cambia la sede, cargar pservicios correspondientes
    public function updatedUsuSede($value)
    {
        $this->usu_pservicio = null;
        if ($value) {
            $this->pservicios = Pservicio::where('sede_id', $value)->get();
        } else {
            $this->pservicios = [];
        }
    }

    public function actualizar()
    {
        $this->authorize('admin.usuarios.update');
        $this->validate([
            'nombres'      => 'required',
            'apellido1' => 'required',
            'email'     => 'required|email',
            'usu_rol'       => 'required'
        ]);

        try {
                // Validar permisos de asignación de roles
                $rolesRestringidos = $this->getRolesRestringidos();
                if (in_array($this->usu_rol, $rolesRestringidos)) {
                    $this->emit('alertError', 'No tiene permisos para asignar el rol ' . $this->usu_rol . '.');
                    return;
                }

                $user = User::find($this->usu_id);
                $currentRole = optional($user->roles->first())->name;
                if ($currentRole) {
                    $user->removeRole($currentRole);
                }
                $user->assignRole($this->usu_rol);
                // Si es Consultor, Coordinador o Administrador, guardar sede y pservicio; si no, limpiarlos
                $rolesConSede = ['Consultor', 'Coordinador', 'Administrador'];
                $sedeId = in_array($this->usu_rol, $rolesConSede) ? $this->usu_sede : null;
                $pservicioId = in_array($this->usu_rol, $rolesConSede) ? $this->usu_pservicio : null;
                
                $user->update([
                    'name'          => $this->nombres,
                    'apellido1'     => $this->apellido1,
                    'apellido2'     => $this->apellido2,
                    'email'         => $this->email,
                    'eps'           => $this->usu_eps,
                    'tdocumento'    => $this->tdoc,
                    'ndocumento'    => $this->ndoc,
                    'sede_id'       => $sedeId,
                    'pservicio_id'  => $pservicioId,
                ]);
            $this->reset();
            $this->emit('alertSuccess','Usuario editado con éxito.');  
        } catch (\Throwable $th) {
            $this->emit('alertError','Error: '.$th.'');
        }
    }

    public function eliminar($id)
    {
        // Solo Super Admin puede eliminar usuarios
        if (!auth()->user()->hasRole('Super Admin')) {
            $this->emit('alertError', 'No tiene permisos para eliminar usuarios.');
            return;
        }

        try {
            $usuario = User::find($id);
            
            if (!$usuario) {
                $this->emit('alertError', 'Usuario no encontrado.');
                return;
            }

            // No permitir eliminar al propio usuario
            if ($usuario->id === auth()->user()->id) {
                $this->emit('alertError', 'No puede eliminar su propio usuario.');
                return;
            }

            // Eliminar el usuario
            $usuario->delete();
            
            $this->emit('alertSuccess', 'Usuario eliminado con éxito.');
        } catch (\Throwable $th) {
            $this->emit('alertError', 'Error al eliminar usuario: ' . $th->getMessage());
        }
    }
}
