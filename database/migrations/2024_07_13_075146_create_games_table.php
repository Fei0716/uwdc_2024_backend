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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->integer('player_count')->default(2);
            $table->enum('privacy',['public' , 'private' ])->default('public');
            $table->enum('mode',['freehand' , 'retrace' ])->default('freehand');
            $table->integer('round_count');
            $table->integer('round_countdown')->nullable();
            $table->string('link');
            $table->longText('drawing_data')->nullable();
            $table->string('final_gif')->nullable();
            $table->boolean('has_started')->default(false);
            $table->boolean('has_ended')->default(false);
            $table->integer('draw_item_index')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
