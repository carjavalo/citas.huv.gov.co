<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class servicios extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'servnomb',
        'servcod',
        'estado',
        'id_pservicios',
    ];

    public function pservicio()
    {
        return $this->belongsTo(Pservicio::class, 'id_pservicios');
    }
}
