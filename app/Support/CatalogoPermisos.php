<?php

namespace App\Support;

/**
 * Catálogo de vistas administrables por el Gestor de Permisos.
 *
 * Cada vista declara qué permiso de Spatie gobierna cada una de sus acciones.
 * Las acciones estándar (ver, crear, editar, borrar) se muestran como columnas
 * de la matriz; las que no encajan en ese molde van en "otras".
 *
 * Los permisos siguen guardándose en las tablas de Spatie (role_has_permissions),
 * así que lo que cada rol tenía antes de existir el gestor se conserva tal cual
 * y el resto del sistema (@can, authorize, middleware can:) no cambia.
 */
class CatalogoPermisos
{
    /** Rol con acceso total (Gate::before): no se configura en el gestor. */
    public const SUPER_ADMIN = 'Super Admin';

    /**
     * Roles cuyo nombre usa el código (registro, redirecciones, tiempos de
     * sesión...). En Gestión Roles no se pueden renombrar ni eliminar.
     */
    public const ROLES_SISTEMA = [
        'Super Admin', 'Administrador', 'Coordinador', 'Consultor',
        'Usuario', 'Hospital', 'Gestor Obstetricia',
    ];

    public const ACCIONES = [
        'ver' => 'Ver',
        'crear' => 'Crear',
        'editar' => 'Editar',
        'borrar' => 'Borrar',
    ];

    /**
     * @var array<int, array{key: string, titulo: string, grupo: string, ruta: string, acciones: array<string, string>, otras?: array<string, string>}>
     */
    public const VISTAS = [
        // Menú principal
        [
            'key' => 'consultar-solicitudes', 'titulo' => 'Consultar solicitudes', 'grupo' => 'Menú principal',
            'ruta' => '/consulta/solicitudes',
            'acciones' => [
                'ver' => 'citas.consulta.solicitudes',
                'editar' => 'citas.consulta.agendar',
                'borrar' => 'citas.solicitudes.eliminar',
            ],
            'otras' => [
                'citas.consulta.notificar' => 'Notificar novedad',
                'citas.solicitudes.reenviar' => 'Volver a notificar (reenvío masivo)',
            ],
        ],
        [
            'key' => 'consultar-usuarios', 'titulo' => 'Consultar usuarios', 'grupo' => 'Menú principal',
            'ruta' => '/usuarios/consulta',
            'acciones' => [
                'ver' => 'admin.usuarios.consult',
                'crear' => 'admin.usuarios.create',
                'editar' => 'admin.usuarios.update',
                'borrar' => 'admin.usuarios.eliminar',
            ],
        ],
        [
            'key' => 'configuracion-eps', 'titulo' => 'Configuración eps', 'grupo' => 'Menú principal',
            'ruta' => '/configuracion/eps/consulta',
            'acciones' => [
                'ver' => 'admin.eps.consultar',
                'editar' => 'admin.eps.edit',
            ],
        ],

        // Configuración servicios
        [
            'key' => 'activar-servicios', 'titulo' => 'Activar servicios', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/servicios/consulta',
            'acciones' => [
                'ver' => 'admin.servicios.consultar',
                'editar' => 'admin.servicios.edit',
            ],
        ],
        [
            'key' => 'sedes', 'titulo' => 'Sede', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/sedes/consulta',
            'acciones' => ['ver' => 'admin.sedes.consultar'],
        ],
        [
            'key' => 'pservicios', 'titulo' => 'Servicios', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/pservicios/consulta',
            'acciones' => ['ver' => 'admin.pservicios.consultar'],
        ],
        [
            'key' => 'especialidades', 'titulo' => 'Especialidades', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/especialidades/consulta',
            'acciones' => ['ver' => 'admin.especialidades.consultar'],
        ],
        [
            'key' => 'campanas', 'titulo' => 'Campañas', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/campanas/consulta',
            'acciones' => ['ver' => 'admin.campanas.consultar'],
        ],
        [
            'key' => 'mensajes-masivos', 'titulo' => 'Mensajes Masivos', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/mensajes-masivos/consulta',
            'acciones' => ['ver' => 'admin.mensajes_masivos.consultar'],
        ],
        [
            'key' => 'roles', 'titulo' => 'Roles', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/roles/consulta',
            'acciones' => ['ver' => 'admin.roles.consultar'],
        ],
        [
            'key' => 'gestion-roles', 'titulo' => 'Gestión Roles', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/gestion-roles',
            'acciones' => [
                'ver' => 'admin.roles.gestionar',
                'crear' => 'admin.roles.crear',
                'editar' => 'admin.roles.editar',
                'borrar' => 'admin.roles.eliminar',
            ],
        ],
        [
            'key' => 'mantenimiento', 'titulo' => 'P_Downd (modo mantenimiento)', 'grupo' => 'Configuración servicios',
            'ruta' => '/configuracion/mantenimiento',
            'acciones' => ['ver' => 'admin.mantenimiento.gestionar'],
        ],

        // Reportes
        [
            'key' => 'reporte-general', 'titulo' => 'Reporte General', 'grupo' => 'Reportes',
            'ruta' => '/reporte/solicitudes/general',
            'acciones' => ['ver' => 'admin.reporte.agentes'],
            'otras' => [
                'admin.reporte.agentes.exportar.hoy' => 'Exportar día actual',
                'admin.reporte.agentes.exportar.filtro_fecha' => 'Exportar por rango de fechas',
            ],
        ],
        [
            'key' => 'reporte-citas', 'titulo' => 'Reporte Citas', 'grupo' => 'Reportes',
            'ruta' => '/reportecitas/export',
            'acciones' => ['ver' => 'admin.reporte.citas'],
        ],
        [
            'key' => 'reporte-especialidades', 'titulo' => 'Reporte Especialidades', 'grupo' => 'Reportes',
            'ruta' => '/reporte/especialidades',
            'acciones' => ['ver' => 'admin.reporte.especialidades'],
        ],
        [
            'key' => 'reporte-ingresos', 'titulo' => 'Reporte Ingresos', 'grupo' => 'Reportes',
            'ruta' => '/reporte/ingresos',
            'acciones' => ['ver' => 'admin.reporte.ingresos'],
        ],

        // Atención
        [
            'key' => 'atencion-telesalud', 'titulo' => 'Atención Telesalud (obstetricia)', 'grupo' => 'Atención',
            'ruta' => '/obstetricia',
            'acciones' => [
                'ver' => 'godson.request.view',
                'crear' => 'godson.request.make',
                'editar' => 'godson.request.attend',
            ],
            'otras' => [
                'menu.atencion_telesalud' => 'Opción en el menú principal',
                'godson.request.check' => 'Pasar a revisión',
                'godson.request.reject' => 'Rechazar',
                'godson.request.cancel' => 'Cancelar solicitud',
            ],
        ],
        [
            'key' => 'atencion-usuarios', 'titulo' => 'Atención Usuarios / Gestionar Usuarios (agenda)', 'grupo' => 'Atención',
            'ruta' => '/modulo/AgendaCitas/menuagenda',
            'acciones' => ['ver' => 'admin.agenda.consultar'],
        ],

        // Menú de la cuenta
        [
            'key' => 'gestionar-servicios', 'titulo' => 'Gestionar Servicios', 'grupo' => 'Menú de la cuenta',
            'ruta' => '/modulo/servicios/servicios',
            'acciones' => ['ver' => 'admin.gestionar_servicios'],
        ],
        [
            'key' => 'operando', 'titulo' => 'Operando (consultas SQL)', 'grupo' => 'Menú de la cuenta',
            'ruta' => '/operando/sql',
            'acciones' => ['ver' => 'admin.operando'],
        ],
    ];

    /**
     * Permisos que crea el gestor (no existían antes) con su descripción.
     * Los demás del catálogo ya estaban en la tabla permissions.
     */
    public const NUEVOS = [
        'citas.solicitudes.eliminar' => 'Eliminar solicitudes de citas',
        'citas.solicitudes.reenviar' => 'Volver a notificar citas agendadas (reenvío masivo)',
        'admin.usuarios.eliminar' => 'Eliminar usuarios',
        'admin.sedes.consultar' => 'Configurar sedes',
        'admin.pservicios.consultar' => 'Configurar servicios',
        'admin.especialidades.consultar' => 'Configurar especialidades',
        'admin.campanas.consultar' => 'Configurar campañas',
        'admin.mensajes_masivos.consultar' => 'Enviar mensajes masivos de citación',
        'admin.roles.consultar' => 'Consultar roles de usuarios',
        'admin.mantenimiento.gestionar' => 'Activar o desactivar el modo mantenimiento por rol',
        'admin.reporte.citas' => 'Consultar reporte de citas',
        'admin.reporte.especialidades' => 'Consultar reporte de especialidades',
        'admin.reporte.ingresos' => 'Consultar reporte de ingresos',
        'menu.atencion_telesalud' => 'Ver la opción Atención Telesalud en el menú',
        'admin.agenda.consultar' => 'Atención de usuarios (agenda de citas)',
        'admin.gestionar_servicios' => 'Gestionar servicios (menú de la cuenta)',
        'admin.operando' => 'Ejecutar consultas SQL (Operando)',
    ];

    /**
     * Todos los permisos de una vista: los de sus acciones y los de "otras".
     *
     * @return list<string>
     */
    public static function permisosDe(array $vista): array
    {
        return array_values(array_unique(array_merge(
            array_values($vista['acciones']),
            array_keys($vista['otras'] ?? [])
        )));
    }

    /**
     * @return list<string>
     */
    public static function todosLosPermisos(): array
    {
        return array_values(array_unique(array_merge(
            ...array_map([self::class, 'permisosDe'], self::VISTAS)
        )));
    }

    /**
     * @return list<string>
     */
    public static function grupos(): array
    {
        return array_values(array_unique(array_column(self::VISTAS, 'grupo')));
    }
}
