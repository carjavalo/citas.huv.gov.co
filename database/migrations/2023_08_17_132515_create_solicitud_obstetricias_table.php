<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudObstetriciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitud_obstetricia', function (Blueprint $table) {
            $table->id();
            $table->string('historia_clinica')->nullable();
            $table->string('documento_identidad');
            $table->string('adjunto_adicional')->nullable();
            $table->string('observacion', 600)->nullable();
            $table->integer('especialidad_id');
            $table->integer('paciente_obstetrica_id');
            $table->integer('user_id');
            $table->integer('hospital_id');
            $table->integer('estado_solicitud_id')->nullable();
            $table->string('observacion_rechazo',1000)->nullable();
            $table->string('observacion_revision',1000)->nullable();
            $table->timestamps();
            $table->date('fecha_atendido')->nullable();
            $table->softDeletes();
            /* $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('paciente_obstetrica_id')->references('id')->on('paciente_obstetrica');
            $table->foreign('especialidad_id')->references('id')->on('servicios'); */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitud_obstetricia');
    }
}
