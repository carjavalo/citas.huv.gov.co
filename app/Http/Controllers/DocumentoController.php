<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentoController extends Controller
{
    /**
     * Sirve un documento desde la carpeta Documentos en public
     * Maneja correctamente espacios y caracteres especiales en nombres de archivo
     * 
     * @param Request $request
     * @param string $path - Ruta codificada en base64
     * @return Response
     */
    public function ver(Request $request, $path)
    {
        // Decodificar la ruta que viene en base64
        $rutaDecodificada = base64_decode($path);
        
        // Construir la ruta completa
        $rutaCompleta = public_path($rutaDecodificada);
        
        // Verificar que el archivo existe
        if (!file_exists($rutaCompleta)) {
            abort(404, 'Documento no encontrado');
        }
        
        // Verificar que la ruta está dentro de Documentos (seguridad)
        $rutaNormalizada = realpath($rutaCompleta);
        $directorioBase = realpath(public_path('Documentos'));
        
        if ($rutaNormalizada === false || strpos($rutaNormalizada, $directorioBase) !== 0) {
            abort(403, 'Acceso no autorizado');
        }
        
        // Obtener el tipo MIME del archivo
        $mimeType = mime_content_type($rutaCompleta);
        
        // Obtener el nombre del archivo para el header
        $nombreArchivo = basename($rutaCompleta);
        
        // Retornar el archivo con headers apropiados
        return response()->file($rutaCompleta, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $nombreArchivo . '"'
        ]);
    }

    /**
     * Genera la URL segura para visualizar un documento
     * 
     * @param string|null $ruta - Ruta relativa del documento (ej: Documentos/usuario123/solicitud_1/archivo.pdf)
     * @return string|null
     */
    public static function generarUrl($ruta)
    {
        if (empty($ruta) || $ruta === 'NULL' || $ruta === null) {
            return null;
        }
        
        // Codificar la ruta en base64 para evitar problemas con caracteres especiales
        $rutaCodificada = base64_encode($ruta);
        
        return route('documento.ver', ['path' => $rutaCodificada]);
    }
}
