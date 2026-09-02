<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade a `solicitudes` los datos que faltaban para poder reconstruir y
 * liberar un agendamiento:
 *
 *  - fecha_cita / hora_cita: hasta ahora la fecha y la hora solo viajaban en el
 *    correo de citación, por lo que una solicitud atascada no se podía
 *    reconstruir ni reenviar.
 *  - procesando_desde: marca cuándo un consultor abrió el modal. Permite
 *    distinguir "se está agendando ahora mismo" de "quedó atascada hace días"
 *    sin depender de updated_at, que cambia por cualquier otro motivo.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitudes', 'fecha_cita')) {
                $table->date('fecha_cita')->nullable()->after('certfdo_cita');
            }
            if (!Schema::hasColumn('solicitudes', 'hora_cita')) {
                $table->time('hora_cita')->nullable()->after('fecha_cita');
            }
            if (!Schema::hasColumn('solicitudes', 'procesando_desde')) {
                $table->timestamp('procesando_desde')->nullable()->after('estado_anterior');
            }
        });

        // Índice para el watchdog: busca por estado + antigüedad del bloqueo.
        if (!$this->indexExists('solicitudes', 'idx_solicitudes_procesando_desde')) {
            Schema::table('solicitudes', function (Blueprint $table) {
                $table->index('procesando_desde', 'idx_solicitudes_procesando_desde');
            });
        }
    }

    public function down()
    {
        if ($this->indexExists('solicitudes', 'idx_solicitudes_procesando_desde')) {
            Schema::table('solicitudes', function (Blueprint $table) {
                $table->dropIndex('idx_solicitudes_procesando_desde');
            });
        }

        Schema::table('solicitudes', function (Blueprint $table) {
            foreach (['fecha_cita', 'hora_cita', 'procesando_desde'] as $columna) {
                if (Schema::hasColumn('solicitudes', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }

    private function indexExists($table, $indexName)
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        return !empty(Schema::getConnection()->select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]
        ));
    }
};
