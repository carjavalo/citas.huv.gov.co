<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixSolicitudesAutoIncrementId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Paso 1: Asegurar que la columna id tenga el atributo AUTO_INCREMENT
        // Esto NO borra datos, solo modifica la definición de la columna
        DB::statement('ALTER TABLE solicitudes MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');

        // Paso 2: Ajustar el contador AUTO_INCREMENT al siguiente valor después del máximo ID
        $maxId = DB::table('solicitudes')->max('id') ?? 0;
        $nextId = $maxId + 1;
        DB::statement("ALTER TABLE solicitudes AUTO_INCREMENT = {$nextId}");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // En la reversión, simplemente resetear al valor anterior
        // Se asume que antes de aplicar esta migración, el AUTO_INCREMENT estaba mal
        // Por seguridad, no hacemos nada en down()
    }
}
