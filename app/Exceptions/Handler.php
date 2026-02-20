<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Detectar error de conexión a la base de datos
        if ($exception instanceof \Illuminate\Database\QueryException) {
            $msg = $exception->getMessage();
            // Errores típicos de conexión a base de datos
            $erroresConexion = [
                'SQLSTATE[HY000] [2002]',
                'No se puede establecer una conexión',
                'Connection refused',
                'SQLSTATE[HY000] [1044]',
                'Acceso denegado para el usuario',
                'Access denied for user',
                'Unknown database',
                'Base de datos desconocida',
            ];
            foreach ($erroresConexion as $error) {
                if (str_contains($msg, $error)) {
                    return response()->view('errors.db-connection', [], 500);
                }
            }
        }
        return parent::render($request, $exception);
    }
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
