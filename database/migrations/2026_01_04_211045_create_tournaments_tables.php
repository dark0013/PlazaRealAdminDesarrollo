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
Schema::create('tournaments', function (Blueprint $table) {
    $table->id();

    $table->string('name');
    $table->date('start_date');
    $table->date('end_date');

    // INDIVIDUAL | DOUBLES
    $table->string('tournament_type', 20);

    // CATEGORY | OPEN
    $table->string('mode', 20)->default('CATEGORY');

    // Relación con category (tabla REAL)
    $table->unsignedBigInteger('category_id')->nullable();

    // PENDING | ACTIVE | FINISHED | CANCELLED
    $table->string('status', 20)->default('PENDING');

    $table->text('description')->nullable();

    $table->timestamps();

    /* $table->foreign('category_id')
        ->references('id')
        ->on('category')   // 👈 nombre correcto
        ->nullOnDelete(); */
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('tournaments');
    }
};
