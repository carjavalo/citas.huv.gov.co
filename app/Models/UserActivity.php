<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo_actividad',
        'descripcion',
        'modulo',
        'accion',
        'datos_adicionales',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_adicionales' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registrar una actividad de usuario
     */
    public static function registrar($userId, $tipo, $descripcion, $modulo = null, $accion = null, $datosAdicionales = null)
    {
        // No registrar actividades del Super Admin
        $user = User::find($userId);
        if ($user && $user->hasRole('Super Admin')) {
            return null;
        }

        return self::create([
            'user_id' => $userId,
            'tipo_actividad' => $tipo,
            'descripcion' => $descripcion,
            'modulo' => $modulo,
            'accion' => $accion,
            'datos_adicionales' => $datosAdicionales,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
