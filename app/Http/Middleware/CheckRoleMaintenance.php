<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CheckRoleMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Verificar si existe la configuración
        if (Storage::disk('local')->exists('maintenance_mode.json')) {
            $config = json_decode(Storage::disk('local')->get('maintenance_mode.json'), true);
            
            // 2. Si el modo mantenimiento está activo
            if (!empty($config['enabled']) && $config['enabled'] === true) {
                
                // 3. Verificar usuario autenticado
                if (Auth::check()) {
                    $user = Auth::user();
                    
                    // IMPORTANTE: Super Admin nunca se bloquea, pase lo que pase
                    if ($user->hasRole('Super Admin')) {
                        return $next($request);
                    }
                    
                    // 4. Verificar roles
                    $blocked_roles = $config['blocked_roles'] ?? [];
                    if (!empty($blocked_roles)) {
                        $user_roles = $user->getRoleNames()->toArray();
                        
                        // Lógica mejorada: Si el usuario tiene AL MENOS UN rol que NO está bloqueado, debería pasar.
                        // Calculamos la diferencia: Roles del usuario que NO están en la lista de bloqueados.
                        $allowed_roles = array_diff($user_roles, $blocked_roles);

                        // Si array_diff devuelve vacío, significa que TODOS sus roles están bloqueados.
                        if (empty($allowed_roles)) {
                            
                            // LOGGING: Registrar quién fue bloqueado para depuración
                            \Illuminate\Support\Facades\Log::warning("Mantenimiento: Bloqueado usuario {$user->email} (Roles: " . implode(', ', $user_roles) . ")");

                            // 5. Lanzar excepción 503 (Service Unavailable)
                            abort(503, $config['message'] ?? 'El sistema se encuentra en mantenimiento.');
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}

