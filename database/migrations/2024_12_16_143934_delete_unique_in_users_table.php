<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']); // Remove a restrição de unicidade
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email'); // Restaura a restrição de unicidade
        });
    }
};
