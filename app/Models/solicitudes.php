<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class solicitudes extends Model
{
    use HasFactory;

    /**
     * Asegurar explícitamente la configuración de la clave primaria
     * para evitar ambientes con comportamiento distinto.
     */
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'pacid',
        'espec',
        'estado',
        'solnum',
        'pachis',
        'pacordmed',
        'pacauto',
        'pacobs',
        'pacdocid',
        'usercod',
        'codigo_autorizacion',
        'motivo_rechazo',
        'motivo_espera',
        'soporte_patologia',
    ];

    protected $table = 'solicitudes';

    /**
     * Boot del modelo - registra actividad cuando se crea una solicitud
     */
    protected static function booted()
    {
        static::created(function ($solicitud) {
            // Registrar actividad de nueva solicitud de cita
            if ($solicitud->pacid && auth()->check()) {
                $user = \App\Models\User::find($solicitud->pacid);
                if ($user && !$user->hasRole('Super Admin')) {
                    \App\Models\UserActivity::create([
                        'user_id' => $solicitud->pacid,
                        'tipo_actividad' => 'cita',
                        'descripcion' => 'Solicitó una cita',
                        'modulo' => 'citas',
                        'accion' => 'crear',
                        'datos_adicionales' => json_encode([
                            'solicitud_id' => $solicitud->id,
                            'servicio_id' => $solicitud->espec,
                        ]),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }
        });
    }

    //Query Scopes 
    /*public function scopefilter($query, $filters){
        $query->when($filters['servicio'] ?? null, function($query, $servicio){
            $query->where('servicio','=', $servicio);
        })->when($filters['fromDate'] ?? null, function($query, $fromDate){
            $query->where('created_at','>=', $fromDate.'00:00:00');
        })->when($filters['toDate'] ?? null, function($query, $toDate){
            $query->where('created_at','<=', $toDate.'23:59:59');
        }); 
       
    }*/

 


    //Relacion Uno a Muchos inversa
    public function User(){
        return $this->belongsTo(User::class, 'pacid', 'id');
    }

    // Relación con Servicio
    public function servicio()
    {
        return $this->belongsTo(servicios::class, 'espec', 'servcod');
    }
    
}
