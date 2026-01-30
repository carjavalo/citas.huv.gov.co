<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuracion_sistema';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    public $timestamps = true;

    /**
     * Obtener el valor de una configuración por su clave
     */
    public static function obtener($clave, $default = null)
    {
        $config = self::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    /**
     * Establecer el valor de una configuración
     */
    public static function establecer($clave, $valor, $descripcion = null)
    {
        return self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'descripcion' => $descripcion]
        );
    }

    /**
     * Verificar si una configuración está habilitada (valor = '1' o 'true')
     */
    public static function estaHabilitado($clave)
    {
        $valor = self::obtener($clave, '0');
        return in_array($valor, ['1', 'true', true, 1], true);
    }
}
