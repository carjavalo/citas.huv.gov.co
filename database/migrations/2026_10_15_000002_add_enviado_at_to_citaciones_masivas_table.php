<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddEnviadoAtToCitacionesMasivasTable extends Migration
{
    // Al reenviar la misma cita se reutiliza el registro: created_at queda con
    // el primer envío y enviado_at guarda el último (fecha del informe y filtros).
    public function up()
    {
        Schema::table('citaciones_masivas', function (Blueprint $table) {
            $table->timestamp('enviado_at')->nullable()->after('sede');
            $table->index('enviado_at');
        });

        DB::table('citaciones_masivas')->whereNull('enviado_at')->update(['enviado_at' => DB::raw('created_at')]);
    }

    public function down()
    {
        Schema::table('citaciones_masivas', function (Blueprint $table) {
            $table->dropIndex(['enviado_at']);
            $table->dropColumn('enviado_at');
        });
    }
}
