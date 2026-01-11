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
       Schema::create('tournament_participants', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('tournament_id');
    $table->unsignedBigInteger('sportsman_id');

    // Para dobles
    $table->unsignedBigInteger('partner_id')->nullable();

    $table->timestamps();

    $table->unique(['tournament_id', 'sportsman_id']);

    $table->foreign('tournament_id')
        ->references('id')
        ->on('tournaments')
        ->cascadeOnDelete();

    $table->foreign('sportsman_id')
        ->references('id')
        ->on('sportsman')   // 👈 nombre correcto
        ->cascadeOnDelete();

    $table->foreign('partner_id')
        ->references('id')
        ->on('sportsman')
        ->nullOnDelete();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_participants');
    }
};
