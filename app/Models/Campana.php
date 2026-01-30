<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campana extends Model
{
    use HasFactory;

    protected $table = 'campanas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen',
        'video',
        'archivo_pdf',
        'categoria',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'visible',
        'color_texto',
        'fuente_texto',
        'tamano_texto',
        'orden',
        'user_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'visible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'publicado')
                    ->where('visible', true)
                    ->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now());
    }

    public function scopeProgramadas($query)
    {
        return $query->where('estado', 'programado')
                    ->where('fecha_inicio', '>', now());
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        return null;
    }

    public function getPdfUrlAttribute()
    {
        if ($this->archivo_pdf) {
            return asset('storage/' . $this->archivo_pdf);
        }
        return null;
    }
}
