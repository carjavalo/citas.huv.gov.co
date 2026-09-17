<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class eps extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Limita la consulta a las eps activas.
     * Se usa en todos los select para que las eps inactivas no se puedan escoger.
     */
    public function scopeActivas($query)
    {
        return $query->where('estado','=',true);
    }
}
