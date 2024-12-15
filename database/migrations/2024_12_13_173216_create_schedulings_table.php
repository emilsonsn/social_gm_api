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
        Schema::create('schedulings', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->unsignedBigInteger('instance_id');
            $table->string('group_id');
            $table->longText('text')->nullable();
            $table->string('video_path')->nullable();
            $table->string('image_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->enum('status', ['Model', 'Waiting', 'Sent', 'Inactive'])->default('Waiting');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('instance_id')->references('id')->on('instances');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedulings');
    }
};
