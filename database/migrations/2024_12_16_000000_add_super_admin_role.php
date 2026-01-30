<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear el rol Super Admin si no existe
        if (!Role::where('name', 'Super Admin')->exists()) {
            Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', 'Super Admin')->delete();
    }
};
