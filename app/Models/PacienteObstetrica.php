<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Model,
    SoftDeletes,
    Factories\HasFactory
};

class PacienteObstetrica extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'paciente_obstetrica';
    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'tipo_documento_id',
        'numero_documento',
        'eps_id',
        'direccion_residencia',
        'telefono'
    ];

    /**
     * Get the eps associated with the patient.
     */
    public function eps()
    {
        return $this->hasOne(eps::class,'id','eps_id');
    }

    /**
     * Get the eps associated with the patient.
     */
    public function tipo_documento()
    {
        return $this->hasOne(tipo_identificacion::class,'id','tipo_documento_id');
    }
}
