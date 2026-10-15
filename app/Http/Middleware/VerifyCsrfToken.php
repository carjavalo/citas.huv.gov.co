<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Confirmación pública de Mensajes Masivos: el token de 48 caracteres del
        // enlace ya es el secreto; así el paciente no ve "419 Page Expired" si
        // responde horas después de abrir el correo.
        'citacion/*',
    ];
}
