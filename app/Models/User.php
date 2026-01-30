<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\MyResetPassword;

use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasRoles;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /* use HasRoles; */


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'apellido1',
        'apellido2',
        'tdocumento',
        'ndocumento',
        'telefono1',
        'telefono2',
        'eps',
        'sede_id',
        'pservicio_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPassword($token));
    }

    public function linkedHospital()
    {
        return $this->hasOne(HospitalXUser::class,'user_id','id');
    }

    //Relacion Uno a Muchos
    public function solicitudes(){
        return $this->hasMany(solicitudes::class);
    }

    // Relación con Sede
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    // Relación con Pservicio
    public function pservicio()
    {
        return $this->belongsTo(Pservicio::class, 'pservicio_id');
    }

    /**
     * Boot del modelo - asigna rol Usuario automáticamente a nuevos registros
     */
    protected static function booted()
    {
        static::created(function ($user) {
            // Asignar rol "Usuario" si no tiene ningún rol asignado
            if ($user->roles->isEmpty()) {
                $user->assignRole('Usuario');
            }
            
            // Refrescar roles después de asignar
            $user->load('roles');
            
            // Registrar actividad de nuevo registro (excepto Super Admin)
            if (!$user->hasRole('Super Admin')) {
                \App\Models\UserActivity::create([
                    'user_id' => $user->id,
                    'tipo_actividad' => 'registro',
                    'descripcion' => 'Usuario registrado en el sistema',
                    'modulo' => 'usuarios',
                    'accion' => 'registro',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });
    }
}
