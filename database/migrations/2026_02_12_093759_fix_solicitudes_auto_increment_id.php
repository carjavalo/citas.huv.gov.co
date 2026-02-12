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
        // Obtener el máximo ID actual de la tabla
        $maxId = DB::table('solicitudes')->max('id') ?? 0;
        $nextId = $maxId + 1;
        
        // Restaurar el AUTO_INCREMENT al siguiente valor después del máximo ID
        // Esto no borra datos, solo configura la secuencia
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
