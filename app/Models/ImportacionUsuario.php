<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Cabecera de una importación de usuarios desde Excel (Usuarios → Importar).
 */
class ImportacionUsuario extends Model
{
    /** Archivo analizado, pendiente de que el usuario confirme la importación. */
    const ANALIZADO   = 'analizado';
    /** Creando usuarios por lotes. */
    const IMPORTANDO  = 'importando';
    const FINALIZADO  = 'finalizado';

    protected $table = 'importaciones_usuarios';

    protected $fillable = [
        'archivo',
        'hoja',
        'importado_por',
        'importado_por_nombre',
        'estado',
        'total_filas',
        'nuevos',
        'importados',
        'existentes',
        'omitidos',
        'sin_eps',
        'finalizado_at',
    ];

    protected $casts = [
        'finalizado_at' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(ImportacionUsuarioDetalle::class, 'importacion_id');
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'importado_por');
    }

    /** Informes que ya se ejecutaron: los análisis sin confirmar no son un informe. */
    public function scopeInformes($query)
    {
        return $query->whereIn('estado', [self::IMPORTANDO, self::FINALIZADO]);
    }

    /** Nombre de quien importó, aunque su usuario ya no exista. */
    public function getAutorNombreAttribute()
    {
        return $this->importado_por_nombre ?: ($this->autor->name ?? 'Usuario eliminado');
    }

    /** Filas que aún no se han procesado (importación a medias). */
    public function getPendientesAttribute()
    {
        return $this->detalles()->where('estado', ImportacionUsuarioDetalle::NUEVO)->count();
    }
}
