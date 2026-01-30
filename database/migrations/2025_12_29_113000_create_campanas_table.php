<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('campanas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->enum('categoria', ['campana', 'noticia', 'urgente', 'servicio'])->default('campana');
            $table->enum('estado', ['borrador', 'publicado', 'programado'])->default('borrador');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('visible')->default(true);
            $table->string('color_texto')->default('#000000');
            $table->string('fuente_texto')->default('Inter');
            $table->string('tamano_texto')->default('16px');
            $table->integer('orden')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('campanas');
    }
};
