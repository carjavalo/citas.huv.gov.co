<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sedes')) {
            Schema::create('sedes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('ciudad')->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });

            // Insertar las sedes iniciales
            DB::table('sedes')->insert([
                ['id' => 1, 'nombre' => 'Sede Cali', 'ciudad' => 'Cali', 'estado' => true, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'nombre' => 'Sede Cartago', 'ciudad' => 'Cartago', 'estado' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sedes');
    }
}
