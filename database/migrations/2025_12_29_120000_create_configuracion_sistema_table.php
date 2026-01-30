<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracion_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar configuración inicial
        DB::table('configuracion_sistema')->insert([
            'clave' => 'seccion_novedades_campanas_habilitada',
            'valor' => '1',
            'descripcion' => 'Habilitar/Deshabilitar la sección de Novedades y Campañas en la vista principal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('configuracion_sistema');
    }
};
