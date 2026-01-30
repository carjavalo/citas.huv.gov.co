<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('apellido1');
            $table->string('apellido2')->nullable();
            $table->string('tdocumento');
            $table->float('ndocumento')->unique();
            $table->string('telefono1',12);
            $table->string('telefono2')->nullable();
            $table->integer('eps');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            /* $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable(); */
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
        Schema::dropIfExists('users');
    }
};
