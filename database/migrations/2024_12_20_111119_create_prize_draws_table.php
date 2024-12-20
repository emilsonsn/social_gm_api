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
        Schema::create('prize_draws', function (Blueprint $table) {
            $table->id();
            $table->string('instance_id');
            $table->string('groups');
            $table->string('groups_name');
            $table->string('prize_name');
            $table->timestamps();
        });

        Schema::create('prize_draw_drawns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prize_draw_id');
            $table->string('name')->nullable();
            $table->string('number');            
            $table->timestamps();

            $table->foreign('prize_draw_id')
                ->references('id')
                ->on('prize_draws')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_draw_drawns');
        Schema::dropIfExists('prize_draws');
    }
};
