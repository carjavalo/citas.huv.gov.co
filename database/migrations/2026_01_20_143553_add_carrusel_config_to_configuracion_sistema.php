<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ConfiguracionSistema;

class AddCarruselConfigToConfiguracionSistema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insertar configuraciones por defecto para el carrusel
        ConfiguracionSistema::establecer(
            'carrusel_campanas_automatico',
            '0',
            'Estado del carrusel automático de campañas (0=desactivado, 1=activado)'
        );

        ConfiguracionSistema::establecer(
            'carrusel_campanas_intervalo',
            '5',
            'Intervalo en segundos del carrusel automático de campañas (3-30)'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar las configuraciones del carrusel
        ConfiguracionSistema::where('clave', 'carrusel_campanas_automatico')->delete();
        ConfiguracionSistema::where('clave', 'carrusel_campanas_intervalo')->delete();
    }
}
