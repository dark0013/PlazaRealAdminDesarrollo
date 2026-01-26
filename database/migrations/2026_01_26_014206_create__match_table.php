<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('player1_id')->nullable();  // permitir nulos
            $table->unsignedBigInteger('player2_id')->nullable();  // permitir nulos

            $table->unsignedBigInteger('winner_id')->nullable();
            $table->unsignedBigInteger('loser_id')->nullable();
            $table->integer('round');  // Ronda del torneo
            $table->timestamps();

            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('player1_id')->references('id')->on('sportsman')->onDelete('cascade');
            $table->foreign('player2_id')->references('id')->on('sportsman')->onDelete('cascade');
            $table->foreign('winner_id')->references('id')->on('sportsman');
            $table->foreign('loser_id')->references('id')->on('sportsman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_match');
    }
};
