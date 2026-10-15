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
    /** El correo no se pudo entregar (la cola agotó los reintentos). */
    const FALLIDO     = 'fallido';

    const ESTADOS = [
        self::PENDIENTE   => 'Sin responder',
        self::ASISTIRA    => 'Confirmó asistencia',
        self::NO_ASISTIRA => 'No asistirá',
        self::FALLIDO     => 'Correo no entregado',
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
        'enviado_at',
        'token',
        'estado',
        'respondido_at',
    ];

    protected $casts = [
        'enviado_at'    => 'datetime',
        'respondido_at' => 'datetime',
    ];

    /**
     * Filtros del informe (y de su exportación a Excel): estado, busqueda,
     * desde/hasta (fecha del último envío) y lote. Sin estado se listan las
     * citaciones enviadas: las de correo no entregado solo con su propio filtro.
     */
    public function scopeFiltrar($query, array $filtros)
    {
        if (!empty($filtros['estado']) && isset(self::ESTADOS[$filtros['estado']])) {
            $query->where('estado', $filtros['estado']);
        } elseif (empty($filtros['incluirFallidos'])) {
            $query->where('estado', '!=', self::FALLIDO);
        }
        if (!empty($filtros['lote'])) {
            $query->where('lote', $filtros['lote']);
        }
        if (!empty($filtros['desde'])) {
            $query->whereDate('enviado_at', '>=', $filtros['desde']);
        }
        if (!empty($filtros['hasta'])) {
            $query->whereDate('enviado_at', '<=', $filtros['hasta']);
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
        return self::baseUrlConfirmacion() . route('citacion.confirmar', $this->token, false);
    }

    /** Dominio del enlace: CITACIONES_URL o, si no está configurado, desde el que se hace el envío. */
    public static function baseUrlConfirmacion()
    {
        $base = trim((string) config('app.url_citaciones'));
        if ($base === '') {
            return rtrim(url('/'), '/');
        }

        // Sin http(s):// el botón del correo quedaría como enlace relativo y no abriría.
        if (!preg_match('#^https?://#i', $base)) {
            $base = 'https://' . ltrim($base, '/');
        }

        return rtrim($base, '/');
    }

    /**
     * true si el enlace solo abre dentro de la red del hospital (IP privada,
     * localhost o nombre sin dominio público): el paciente no podría confirmar desde su casa.
     */
    public static function enlaceSoloRedInterna()
    {
        $host = strtolower(rtrim(trim((string) parse_url(self::baseUrlConfirmacion(), PHP_URL_HOST), '[]'), '.'));
        if ($host === '') {
            return true;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            // 100.64.0.0/10 (CGNAT) tampoco es alcanzable desde internet.
            $cgnat = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                && (ip2long($host) & 0xFFC00000) === (ip2long('100.64.0.0') & 0xFFC00000);

            return $cgnat || !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        if ($host === 'localhost' || strpos($host, '.') === false) {
            return true;
        }

        return (bool) preg_match('/\.(local|localhost|lan|internal|intranet|home\.arpa)$/', $host);
    }
}
