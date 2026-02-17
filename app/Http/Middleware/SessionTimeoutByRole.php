<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeoutByRole
{
    /**
     * Tiempo de inactividad en segundos para el rol Consultor (20 minutos).
     */
    const CONSULTOR_TIMEOUT = 1200; // 20 * 60

    /**
     * Maneja la solicitud verificando el tiempo de inactividad según el rol del usuario.
     *
     * Los usuarios con rol "Consultor" tienen un límite de 20 minutos de inactividad.
     * Los demás roles mantienen el tiempo de sesión configurado por defecto.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Solo aplicar timeout especial al rol Consultor
            if ($user->hasRole('Consultor')) {
                $lastActivity = session('last_activity_time');
                $now = time();

                if ($lastActivity && ($now - $lastActivity) > self::CONSULTOR_TIMEOUT) {
                    // Expiró por inactividad: cerrar sesión
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->with('status', 'Su sesión ha expirado por inactividad (20 minutos).');
                }

                // Actualizar marca de última actividad
                session(['last_activity_time' => $now]);
            }
        }

        return $next($request);
    }
}
