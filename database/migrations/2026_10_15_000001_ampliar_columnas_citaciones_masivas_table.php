<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AmpliarColumnasCitacionesMasivasTable extends Migration
{
    // DIA, FECHA DE CITA y HORA DE CITA vienen como texto libre del Excel: con
    // 30 caracteres MySQL (modo estricto) rechazaba la fila y el correo no salía.
    // Sin doctrine/dbal, ->change() no está disponible: se usa ALTER directo.
    public function up()
    {
        DB::statement('ALTER TABLE citaciones_masivas
            MODIFY dia VARCHAR(100) NULL,
            MODIFY fecha_cita VARCHAR(100) NULL,
            MODIFY hora VARCHAR(100) NULL');
    }

    public function down()
    {
        // En modo estricto el ALTER falla si quedan valores de más de 30 caracteres.
        DB::statement('UPDATE citaciones_masivas SET dia = LEFT(dia, 30), fecha_cita = LEFT(fecha_cita, 30), hora = LEFT(hora, 30)');
        DB::statement('ALTER TABLE citaciones_masivas
            MODIFY dia VARCHAR(30) NULL,
            MODIFY fecha_cita VARCHAR(30) NULL,
            MODIFY hora VARCHAR(30) NULL');
    }
}
