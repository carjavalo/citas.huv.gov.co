<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitacionesMasivasTable extends Migration
{
    public function up()
    {
        // Cada citación enviada desde Mensajes Masivos y la respuesta del paciente.
        Schema::create('citaciones_masivas', function (Blueprint $table) {
            $table->id();
            $table->string('lote', 36); // un envío (clic en "Enviar") agrupa varias citaciones
            $table->string('archivo')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // paciente
            $table->unsignedBigInteger('enviado_por')->nullable();
            $table->string('documento', 30);
            $table->string('paciente');
            $table->string('email');
            $table->string('especialidad')->nullable();
            $table->string('dia', 30)->nullable();
            $table->string('fecha_cita', 30)->nullable();
            $table->string('hora', 30)->nullable();
            $table->string('especialista')->nullable();
            $table->string('sede', 20);
            $table->string('token', 64)->unique();
            $table->string('estado', 20)->default('pendiente'); // pendiente, asistira, no_asistira
            $table->timestamp('respondido_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('lote');
            $table->index(['estado', 'created_at']);
            $table->index('documento');
        });
    }

    public function down()
    {
        Schema::dropIfExists('citaciones_masivas');
    }
}
