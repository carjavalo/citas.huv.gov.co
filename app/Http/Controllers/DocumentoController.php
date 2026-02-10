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
     * @param string $path - Ruta codificada en base64 URL-safe
     * @return Response
     */
    public function ver(Request $request, $path)
    {
        // Decodificar la ruta que viene en base64 URL-safe
        $rutaDecodificada = self::base64UrlDecode($path);
        
        return $this->servirDocumento($rutaDecodificada);
    }

    /**
     * Sirve un documento accedido directamente por la ruta /Documentos/{path}
     * Esta ruta captura URLs antiguas o accesos directos
     * 
     * @param Request $request
     * @param string $path - Ruta relativa del archivo (sin el prefijo Documentos/)
     * @return Response
     */
    public function verDirecto(Request $request, $path)
    {
        // Construir la ruta completa con el prefijo Documentos/
        $rutaRelativa = 'Documentos/' . $path;
        
        return $this->servirDocumento($rutaRelativa);
    }

    /**
     * Método común para servir documentos
     * 
     * @param string $rutaRelativa - Ruta relativa del documento
     * @return Response
     */
    private function servirDocumento($rutaRelativa)
    {
        // Decodificar caracteres URL-encoded que puedan quedar (%20, %C3%93, etc.)
        $rutaRelativa = urldecode($rutaRelativa);
        
        // Construir la ruta completa
        $rutaCompleta = public_path($rutaRelativa);
        
        // Verificar que el archivo existe
        if (!file_exists($rutaCompleta)) {
            // Intentar buscar el archivo en el directorio por coincidencia parcial
            $directorio = dirname($rutaCompleta);
            $nombreArchivo = basename($rutaCompleta);
            
            if (is_dir($directorio)) {
                // Buscar archivos en el directorio y comparar nombres normalizados
                $archivos = scandir($directorio);
                foreach ($archivos as $archivo) {
                    // Comparar el nombre normalizado (sin encoding) 
                    if ($this->normalizarNombre($archivo) === $this->normalizarNombre($nombreArchivo)) {
                        $rutaCompleta = $directorio . DIRECTORY_SEPARATOR . $archivo;
                        break;
                    }
                }
            }
            
            // Si aún no existe, retornar 404
            if (!file_exists($rutaCompleta)) {
                abort(404, 'Documento no encontrado');
            }
        }
        
        // Verificar que la ruta está dentro de Documentos (seguridad)
        $rutaNormalizada = realpath($rutaCompleta);
        $directorioBase = realpath(public_path('Documentos'));
        
        if ($rutaNormalizada === false || $directorioBase === false || strpos($rutaNormalizada, $directorioBase) !== 0) {
            abort(403, 'Acceso no autorizado');
        }
        
        // Obtener el tipo MIME del archivo
        $mimeType = mime_content_type($rutaCompleta);
        
        // Obtener el nombre del archivo para el header
        $nombreArchivo = basename($rutaCompleta);
        
        // Retornar el archivo con headers apropiados
        return response()->file($rutaCompleta, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . rawurlencode($nombreArchivo) . '"'
        ]);
    }

    /**
     * Normaliza un nombre de archivo para comparación
     * Convierte a minúsculas y normaliza caracteres Unicode
     */
    private function normalizarNombre($nombre)
    {
        // Normalizar a forma NFC de Unicode si la extensión intl está disponible
        if (function_exists('normalizer_normalize')) {
            $nombre = \Normalizer::normalize($nombre, \Normalizer::FORM_C);
        }
        return mb_strtolower($nombre, 'UTF-8');
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
        
        // Codificar la ruta en base64 URL-safe para evitar problemas con caracteres especiales
        $rutaCodificada = self::base64UrlEncode($ruta);
        
        return route('documento.ver', ['path' => $rutaCodificada]);
    }

    /**
     * Codifica una cadena en base64 URL-safe
     */
    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodifica una cadena base64 URL-safe
     */
    private static function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
