<?php

use App\Support\CatalogoPermisos;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crea los permisos que necesita el Gestor de Permisos para las opciones que
 * hasta ahora se restringían con el nombre del rol (@role, @unlessrole,
 * middleware role:) y los asigna de forma que cada rol conserve exactamente
 * el acceso que tenía. Super Admin no necesita nada: Gate::before le da todo.
 */
class CrearPermisosGestorPermisos extends Migration
{
    public function up()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (CatalogoPermisos::NUEVOS as $nombre => $desc) {
            Permission::firstOrCreate(
                ['name' => $nombre, 'guard_name' => 'web'],
                ['desc' => $desc]
            );
        }

        // Sede, Servicios, Especialidades y Campañas: se mostraban a quien
        // tuviera admin.servicios.consultar, salvo al Coordinador.
        $this->asignarA(
            $this->rolesCon('admin.servicios.consultar')->where('name', '!=', 'Coordinador'),
            ['admin.sedes.consultar', 'admin.pservicios.consultar', 'admin.especialidades.consultar', 'admin.campanas.consultar']
        );

        // Reportes Citas y Especialidades: estaban en el desplegable Reportes,
        // visible para quien tuviera admin.reporte.agentes.
        $this->asignarA(
            $this->rolesCon('admin.reporte.agentes'),
            ['admin.reporte.citas', 'admin.reporte.especialidades']
        );

        // Exportar el reporte general: estos roles exportaban por nombre de rol
        // aunque no tuvieran el permiso (Reporte\Agente\Procesado::puedeExportar).
        $this->asignarA(
            Role::whereIn('name', ['Admin', 'Administrador', 'Coordinador'])->get(),
            ['admin.reporte.agentes.exportar.hoy', 'admin.reporte.agentes.exportar.filtro_fecha']
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down()
    {
        Permission::whereIn('name', array_keys(CatalogoPermisos::NUEVOS))->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function rolesCon(string $permiso)
    {
        return Role::whereHas('permissions', fn ($q) => $q->where('name', $permiso))->get();
    }

    private function asignarA($roles, array $permisos): void
    {
        foreach ($roles as $rol) {
            $rol->givePermissionTo($permisos);
        }
    }
}
