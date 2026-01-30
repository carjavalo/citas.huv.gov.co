<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'estado_solicitud';
    protected $filable = [
        "descripcion",
        "activo",
        "cita",
        "alto_riesgo_obstetrico"
    ];

    const ESTADOS = [
        "PENDIENTE" => 1,
        "EN_REVISION" => 2,
        "ATENDIDO" => 3,
        "RECHAZADO" => 4,
        "CANCELADO" => 5
    ];
}
