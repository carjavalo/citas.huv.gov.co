<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadoSolicitudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estado_solicitud', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->boolean('activo')->nullable();
            $table->boolean('cita')->nullable();
            $table->boolean('alto_riesgo_obstetrico')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estado_solicitud');
    }
}
