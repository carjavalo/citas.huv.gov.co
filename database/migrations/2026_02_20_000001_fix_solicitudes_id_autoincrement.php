<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixSolicitudesIdAutoincrement20260220 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Corrige la columna id a BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
        DB::statement('ALTER TABLE solicitudes MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No se recomienda revertir, pero si es necesario, cambiar a tipo texto (como estaba antes)
        DB::statement('ALTER TABLE solicitudes MODIFY id TEXT NULL');
    }
}
