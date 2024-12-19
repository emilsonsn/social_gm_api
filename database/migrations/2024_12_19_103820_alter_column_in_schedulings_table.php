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
            $table->enum('status', ['Model', 'Copy', 'Waiting', 'Sent', 'Inactive'])
                ->default('Waiting')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedulings', function (Blueprint $table) {
            $table->enum('status', ['Model', 'Waiting', 'Sent', 'Inactive'])
            ->default('Waiting')
            ->change();            
        });
    }
};
