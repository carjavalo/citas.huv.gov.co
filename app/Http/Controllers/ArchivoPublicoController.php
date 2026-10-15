<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchivoPublicoController extends Controller
{
    /**
     * Sirve los archivos del disco "public" (storage/app/public) en /storage/*,
     * por ejemplo las imágenes, videos y PDF de Campañas.
     *
     * Solo se ejecuta cuando el servidor no tiene el enlace public/storage
     * (php artisan storage:link): si el enlace existe, el servidor web entrega
     * el archivo directamente y la petición nunca llega a Laravel.
     *
     * BinaryFileResponse envía el tipo MIME correcto y atiende peticiones Range,
     * que el navegador usa para reproducir y adelantar los <video>.
     *
     * @param string $path - Ruta relativa dentro de storage/app/public
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function mostrar($path)
    {
        $path = str_replace('\\', '/', $path);

        if ($path === '' || Str::contains($path, ['..', "\0"])) {
            abort(404);
        }

        $raiz = realpath(Storage::disk('public')->path(''));
        $archivo = realpath(Storage::disk('public')->path($path));

        // El archivo debe existir y quedar dentro de storage/app/public
        if ($raiz === false || $archivo === false || !is_file($archivo)
            || !Str::startsWith($archivo, rtrim($raiz, '\\/') . DIRECTORY_SEPARATOR)) {
            abort(404);
        }

        return response()->file($archivo, [
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }
}
