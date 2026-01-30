<?php

namespace Database\Seeders;

use App\Models\EstadoSolicitud;
use Illuminate\Database\Seeder;

class EstadoSolicitudTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EstadoSolicitud::create([
            "descripcion" => "Pendiente",
            "activo" => true,
            "alto_riesgo_obstetrico" => true
        ]);
        EstadoSolicitud::create([
            "descripcion" => "En revisión",
            "activo" => true,
            "alto_riesgo_obstetrico" => true
        ]);
        EstadoSolicitud::create([
            "descripcion" => "Atendido",
            "activo" => true,
            "alto_riesgo_obstetrico" => true
        ]);
        EstadoSolicitud::create([
            "descripcion" => "Rechazado",
            "activo" => true,
            "alto_riesgo_obstetrico" => true
        ]);
        EstadoSolicitud::create([
            "descripcion" => "Cancelado",
            "activo" => true,
            "alto_riesgo_obstetrico" => true
        ]);
    }
}
