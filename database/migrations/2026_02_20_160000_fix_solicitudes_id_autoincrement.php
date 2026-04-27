<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixSolicitudesIdAutoincrement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Asegurar que el campo id sea autoincremental y clave primaria
        DB::statement('ALTER TABLE solicitudes MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No revertimos, solo dejamos el campo como estaba
    }
}