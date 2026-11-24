<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 'enviado_at' se escribe cuando la citación se ENCOLA, no cuando el correo
 * sale: un correo atascado en la cola se veía igual que uno entregado. Esta
 * columna la escribe el listener de MessageSent, ya con el correo aceptado
 * por el servidor SMTP.
 *
 * No se rellena para los envíos anteriores: no hay forma de saber cuáles
 * salieron. El informe los muestra como "sin dato" comparando enviado_at
 * contra CitacionMasiva::SEGUIMIENTO_DESDE.
 */
class AddEntregadoAtToCitacionesMasivasTable extends Migration
{
    public function up()
    {
        Schema::table('citaciones_masivas', function (Blueprint $table) {
            $table->timestamp('entregado_at')->nullable()->after('enviado_at');
            // El informe filtra y cuenta por esta columna en cada carga.
            $table->index('entregado_at');
        });
    }

    public function down()
    {
        Schema::table('citaciones_masivas', function (Blueprint $table) {
            $table->dropIndex(['entregado_at']);
            $table->dropColumn('entregado_at');
        });
    }
}
