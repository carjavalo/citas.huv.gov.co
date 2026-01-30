<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pservicio extends Model
{
    use HasFactory;
    protected $table = 'pservicios';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'sede_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function servicios()
    {
        return $this->hasMany(servicios::class, 'id_pservicios');
    }
}
