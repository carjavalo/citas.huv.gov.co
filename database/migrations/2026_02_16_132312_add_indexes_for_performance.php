<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesForPerformance extends Migration
{
    /**
     * Run the migrations.
     * Agrega índices a las tablas existentes para mejorar el rendimiento
     * Sin eliminar datos de la base de datos
     *
     * @return void
     */
    public function up()
    {
        // Índices para la tabla solicitudes
        if (Schema::hasTable('solicitudes')) {
            Schema::table('solicitudes', function (Blueprint $table) {
                // Índice para búsquedas por estado y pacid
                if (!Schema::hasColumn('solicitudes', 'estado') || !$this->indexExists('solicitudes', 'idx_solicitudes_estado_pacid')) {
                    $table->index(['estado', 'pacid'], 'idx_solicitudes_estado_pacid');
                }
            });
        }

        // Índices para la tabla users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Índice para búsquedas por ndocumento
                if (!$this->indexExists('users', 'idx_users_ndocumento')) {
                    $table->index('ndocumento', 'idx_users_ndocumento');
                }
                // Índice para búsquedas por sede_id
                if (!$this->indexExists('users', 'idx_users_sede_id')) {
                    $table->index('sede_id', 'idx_users_sede_id');
                }
            });
        }

        // Índices para la tabla servicios
        if (Schema::hasTable('servicios')) {
            Schema::table('servicios', function (Blueprint $table) {
                // Índice para búsquedas por estado
                if (!$this->indexExists('servicios', 'idx_servicios_estado')) {
                    $table->index('estado', 'idx_servicios_estado');
                }
            });
        }

        // Índices para la tabla eps
        if (Schema::hasTable('eps')) {
            Schema::table('eps', function (Blueprint $table) {
                // Índice para búsquedas por nombre
                if (!$this->indexExists('eps', 'idx_eps_nombre')) {
                    $table->index('nombre', 'idx_eps_nombre');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar índices en caso de rollback
        if (Schema::hasTable('solicitudes')) {
            Schema::table('solicitudes', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_solicitudes_estado_pacid');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_users_ndocumento');
                $table->dropIndexIfExists('idx_users_sede_id');
            });
        }

        if (Schema::hasTable('servicios')) {
            Schema::table('servicios', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_servicios_estado');
            });
        }

        if (Schema::hasTable('eps')) {
            Schema::table('eps', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_eps_nombre');
            });
        }
    }

    /**
     * Verifica si un índice existe en la tabla
     */
    private function indexExists($table, $indexName)
    {
        $conn = Schema::getConnection();
        $indexList = $conn->select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        return !empty($indexList);
    }
}

