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
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tournament_id');

            $table->unsignedBigInteger('player1_id');
            $table->unsignedBigInteger('player2_id');

            $table->unsignedBigInteger('winner_id')->nullable();

            // OCTAVOS | CUARTOS | SEMIFINAL | FINAL
            $table->string('round', 20);

            $table->date('match_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->string('score')->nullable();

            $table->timestamps();

            $table
                ->foreign('tournament_id')
                ->references('id')
                ->on('tournaments')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
    }
};
