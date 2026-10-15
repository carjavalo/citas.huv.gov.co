<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Citación enviada desde Mensajes Masivos. El paciente responde desde el
 * enlace del correo (token) si asistirá o no a la cita.
 */
class CitacionMasiva extends Model
{
    protected $table = 'citaciones_masivas';

    const PENDIENTE   = 'pendiente';
    const ASISTIRA    = 'asistira';
    const NO_ASISTIRA = 'no_asistira';

    const ESTADOS = [
        self::PENDIENTE   => 'Sin responder',
        self::ASISTIRA    => 'Confirmó asistencia',
        self::NO_ASISTIRA => 'No asistirá',
    ];

    protected $fillable = [
        'lote',
        'archivo',
        'user_id',
        'enviado_por',
        'documento',
        'paciente',
        'email',
        'especialidad',
        'dia',
        'fecha_cita',
        'hora',
        'especialista',
        'sede',
        'token',
        'estado',
        'respondido_at',
    ];

    protected $casts = [
        'respondido_at' => 'datetime',
    ];

    /**
     * Filtros del informe (y de su exportación a Excel): estado, busqueda,
     * desde/hasta (fecha de envío) y lote.
     */
    public function scopeFiltrar($query, array $filtros)
    {
        if (!empty($filtros['estado']) && isset(self::ESTADOS[$filtros['estado']])) {
            $query->where('estado', $filtros['estado']);
        }
        if (!empty($filtros['lote'])) {
            $query->where('lote', $filtros['lote']);
        }
        if (!empty($filtros['desde'])) {
            $query->whereDate('created_at', '>=', $filtros['desde']);
        }
        if (!empty($filtros['hasta'])) {
            $query->whereDate('created_at', '<=', $filtros['hasta']);
        }
        if (!empty($filtros['busqueda'])) {
            $busqueda = '%' . trim($filtros['busqueda']) . '%';
            $query->where(function ($q) use ($busqueda) {
                $q->where('paciente', 'like', $busqueda)
                    ->orWhere('documento', 'like', $busqueda)
                    ->orWhere('email', 'like', $busqueda)
                    ->orWhere('especialidad', 'like', $busqueda)
                    ->orWhere('especialista', 'like', $busqueda);
            });
        }

        return $query;
    }

    public function getEstadoTextoAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    /** Enlace del botón "Ir a confirmar". CITACIONES_URL permite usar el dominio público. */
    public function urlConfirmacion()
    {
        $base = config('app.url_citaciones');
        $ruta = route('citacion.confirmar', $this->token, false);

        return $base ? rtrim($base, '/') . $ruta : url($ruta);
    }
}
