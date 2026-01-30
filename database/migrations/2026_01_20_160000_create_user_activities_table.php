<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tipo_actividad'); // login, logout, registro, cita, accion
            $table->string('descripcion')->nullable();
            $table->string('modulo')->nullable(); // usuarios, citas, configuracion, etc.
            $table->string('accion')->nullable(); // crear, editar, eliminar, ver
            $table->text('datos_adicionales')->nullable(); // JSON con información extra
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
            $table->index('tipo_actividad');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
}
