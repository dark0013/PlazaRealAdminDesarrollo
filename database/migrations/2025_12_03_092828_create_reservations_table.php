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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sportsman_id')->nullable();

            $table->unsignedBigInteger('court_id');

            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->integer('duration')->nullable();  // minutos (calculado)
            $table->string('status');  // PENDING, APPROVED, CANCELLED, BLOCKED
            $table->string('reservation_type');  // NORMAL, TOURNAMENT, MAINTENANCE
            $table->string('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
