<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Una fila del Excel importado y lo que se hizo con ella.
 */
class ImportacionUsuarioDetalle extends Model
{
    /** Pasa la validación: se creará al confirmar la importación. */
    const NUEVO     = 'nuevo';
    const IMPORTADO = 'importado';
    /** Su identificación o su correo ya estaban en la tabla users. */
    const EXISTENTE = 'existente';
    /** No se pudo importar (sin identificación, sin correo, repetida en el archivo...). */
    const OMITIDO   = 'omitido';

    /** Etiqueta que se muestra en el informe por cada estado. */
    const ESTADOS = [
        self::NUEVO     => 'Por importar',
        self::IMPORTADO => 'Importado',
        self::EXISTENTE => 'Ya existía',
        self::OMITIDO   => 'Omitido',
    ];

    protected $table = 'importaciones_usuarios_detalle';

    public $timestamps = false;

    protected $fillable = [
        'importacion_id',
        'fila',
        'user_id',
        'ndocumento',
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'telefono1',
        'telefono2',
        'responsable',
        'eps_id',
        'eps_nombre',
        'estado',
        'motivo',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function importacion()
    {
        return $this->belongsTo(ImportacionUsuario::class, 'importacion_id');
    }

    public function getEstadoTextoAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getNombreCompletoAttribute()
    {
        return trim(preg_replace('/\s+/', ' ', $this->nombre . ' ' . $this->apellido1 . ' ' . $this->apellido2));
    }
}
