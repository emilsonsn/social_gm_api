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
        Schema::create('triggerings', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->unsignedBigInteger('contact_list_id');
            $table->unsignedBigInteger('user_id');
            $table->string('evo_url');
            $table->string('evo_key');
            $table->string('evo_instance');
            $table->integer('interval');
            $table->string('path');
            $table->enum('status', ['Pending', 'Finished'])->default('Pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contact_list_id')
                ->references('id')
                ->on('contact_lists');

                $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });

        Schema::create('triggering_messages', function (Blueprint $table) {
            $table->id();
            $table->longText('message');
            $table->unsignedBigInteger('triggering_id');            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('triggering_id')
                ->references('id')
                ->on('triggerings')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triggering_messages');
        Schema::dropIfExists('triggerings');
    }
};
