<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedulings', function (Blueprint $table) {
            $table->boolean('mention')->default(false)->nullable()->change();
            $table->string('group_id')->nullable()->change();
            $table->string('group_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedulings', function (Blueprint $table) {
            $table->boolean('mention')->default(false)->change();
            $table->string('group_id')->change();
            $table->string('group_name')->change();
        });
    }
};