<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserActivity;
use Illuminate\Support\Str;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo registrar si el usuario está autenticado
        if (auth()->check()) {
            $user = auth()->user();
            
            // No registrar actividades del Super Admin
            if ($user->hasRole('Super Admin')) {
                return $response;
            }

            // Obtener información de la ruta
            $route = $request->route();
            $method = $request->method();
            $path = $request->path();

            // Ignorar rutas de assets, livewire updates, y otras rutas técnicas
            $ignoredPaths = [
                'livewire/message',
                'livewire/upload',
                '_debugbar',
                'sanctum/csrf-cookie',
                'api/',
            ];

            foreach ($ignoredPaths as $ignored) {
                if (Str::contains($path, $ignored)) {
                    return $response;
                }
            }

            // Solo registrar acciones GET en rutas específicas
            if ($method === 'GET') {
                $allowedGetPaths = [
                    'usuarios/consulta',
                    'consulta/solicitudes',
                    'configuracion/',
                    'reporte/',
                ];
                
                $shouldLog = false;
                foreach ($allowedGetPaths as $allowed) {
                    if (Str::contains($path, $allowed)) {
                        $shouldLog = true;
                        break;
                    }
                }
                
                if (!$shouldLog) {
                    return $response;
                }
            }

            // Determinar el módulo y acción basado en la ruta
            $modulo = $this->determinarModulo($path);
            $accion = $this->determinarAccion($method, $path);
            $descripcion = $this->generarDescripcion($method, $path, $modulo, $accion);

            // Registrar la actividad
            if ($descripcion) {
                UserActivity::registrar(
                    $user->id,
                    'accion',
                    $descripcion,
                    $modulo,
                    $accion,
                    [
                        'ruta' => $path,
                        'metodo' => $method,
                    ]
                );
            }
        }

        return $response;
    }

    private function determinarModulo($path)
    {
        if (Str::contains($path, 'usuarios')) return 'usuarios';
        if (Str::contains($path, 'solicitud') || Str::contains($path, 'cita')) return 'citas';
        if (Str::contains($path, 'configuracion')) return 'configuracion';
        if (Str::contains($path, 'reporte')) return 'reportes';
        if (Str::contains($path, 'eps')) return 'eps';
        if (Str::contains($path, 'servicio')) return 'servicios';
        if (Str::contains($path, 'campana')) return 'campanas';
        if (Str::contains($path, 'roles')) return 'roles';
        
        return 'general';
    }

    private function determinarAccion($method, $path)
    {
        if ($method === 'GET') {
            if (Str::contains($path, 'consulta') || Str::contains($path, 'reporte')) {
                return 'consultar';
            }
            return 'ver';
        }
        if ($method === 'POST') return 'crear';
        if ($method === 'PUT' || $method === 'PATCH') return 'editar';
        if ($method === 'DELETE') return 'eliminar';
        
        return 'accion';
    }

    private function generarDescripcion($method, $path, $modulo, $accion)
    {
        // No registrar vistas del dashboard principal
        if ($path === '/' || $path === 'dashboard') {
            return null;
        }

        $descripciones = [
            'usuarios' => [
                'consultar' => 'Consultó lista de usuarios',
                'ver' => 'Vio información de usuarios',
                'crear' => 'Creó un usuario',
                'editar' => 'Editó un usuario',
                'eliminar' => 'Eliminó un usuario',
            ],
            'citas' => [
                'consultar' => 'Consultó solicitudes de citas',
                'ver' => 'Vio detalles de citas',
                'crear' => 'Solicitó una cita',
                'editar' => 'Modificó una solicitud de cita',
                'eliminar' => 'Canceló una cita',
            ],
            'configuracion' => [
                'consultar' => 'Accedió a configuración',
                'ver' => 'Vio configuración',
                'crear' => 'Creó configuración',
                'editar' => 'Modificó configuración',
            ],
            'reportes' => [
                'consultar' => 'Consultó reportes',
                'ver' => 'Vio reportes',
            ],
        ];

        return $descripciones[$modulo][$accion] ?? "Realizó acción en {$modulo}";
    }
}
