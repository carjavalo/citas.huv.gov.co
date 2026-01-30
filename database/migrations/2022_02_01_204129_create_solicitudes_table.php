<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->integer('pacid');
            $table->string('espec');
            $table->string('estado');
            $table->integer('solnum');
            $table->string('pachis');
            $table->string('pacordmed');
            $table->string('pacauto')->nullable();
            $table->string('pacdocid');
            $table->string('usercod')->nullable();
            $table->string('pacobs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes');
    }
}
