<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Model,
    SoftDeletes,
    Factories\HasFactory
};
use \Venturecraft\Revisionable\RevisionableTrait;

class SolicitudObstetricia extends Model
{
    use HasFactory;
    use SoftDeletes;
    use RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    
    protected $table = 'solicitud_obstetricia';
    protected $fillable = [
        'especialidad_id',
        'path_historia',
        'path_documento',
        'observacion'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    /**
     * Get the pacient associated with the request.
     */
    public function paciente()
    {
        return $this->hasOne(PacienteObstetrica::class,'id','paciente_obstetrica_id');
    }

    /**
     * Get the service associated with the request.
     */
    public function especialidad()
    {
        return $this->hasOne(servicios::class,'id','especialidad_id');
    }

    /**
     * Get the service associated with the request.
     */
    public function estado()
    {
        return $this->hasOne(EstadoSolicitud::class,'id','estado_solicitud_id');
    }

    /**
     * Get the hosital associated with the request.
     */
    public function hospital()
    {
        return $this->hasOne(Hospital::class,'id','hospital_id');
    }
}
