<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Historial del módulo Usuarios → Importar: qué archivo se subió, cuántos
 * registros entraron, cuántos ya existían y qué pasó con cada fila.
 *
 * El detalle se guarda al analizar el archivo (estado "nuevo") y la creación
 * de usuarios avanza por lotes sobre esas filas. Así la importación muestra
 * progreso real, puede reanudarse y no viaja el archivo entero en cada
 * petición de Livewire.
 */
class CreateImportacionesUsuariosTable extends Migration
{
    public function up()
    {
        // Cabecera: un archivo analizado y, si se confirma, importado.
        Schema::create('importaciones_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('archivo')->nullable();
            $table->string('hoja')->nullable();
            $table->unsignedBigInteger('importado_por')->nullable();
            // El nombre se guarda aparte: el informe debe seguir leyéndose
            // aunque después se elimine el usuario que hizo la importación.
            $table->string('importado_por_nombre')->nullable();
            // analizado (sin confirmar), importando, finalizado
            $table->string('estado', 20)->default('analizado');
            $table->unsignedInteger('total_filas')->default(0);
            // Filas que pasaron la validación y quedaron por crear: con ellas se
            // calcula el avance mientras se importa.
            $table->unsignedInteger('nuevos')->default(0);
            $table->unsignedInteger('importados')->default(0);
            $table->unsignedInteger('existentes')->default(0);
            $table->unsignedInteger('omitidos')->default(0);
            $table->unsignedInteger('sin_eps')->default(0);
            $table->timestamp('finalizado_at')->nullable();
            $table->timestamps();

            $table->foreign('importado_por')->references('id')->on('users')->onDelete('set null');
            $table->index(['estado', 'created_at']);
        });

        // Detalle: una fila del Excel y lo que se hizo con ella.
        Schema::create('importaciones_usuarios_detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('importacion_id');
            $table->unsignedInteger('fila')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ndocumento', 50)->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono1', 30)->nullable();
            $table->string('telefono2', 30)->nullable();
            $table->string('responsable')->nullable();
            $table->unsignedBigInteger('eps_id')->nullable();
            $table->string('eps_nombre')->nullable();
            // nuevo (por importar), importado, existente, omitido
            $table->string('estado', 20);
            $table->string('motivo')->nullable();   // por qué no se importó
            $table->timestamp('created_at')->nullable();

            $table->foreign('importacion_id')->references('id')->on('importaciones_usuarios')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['importacion_id', 'estado']);
            $table->index('ndocumento');
        });
    }

    public function down()
    {
        Schema::dropIfExists('importaciones_usuarios_detalle');
        Schema::dropIfExists('importaciones_usuarios');
    }
}
