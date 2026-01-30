<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;

    protected $table = 'sedes';

    protected $fillable = [
        'nombre',
        'ciudad',
        'estado',
    ];

    public function pservicios()
    {
        return $this->hasMany(Pservicio::class, 'sede_id');
    }
}
