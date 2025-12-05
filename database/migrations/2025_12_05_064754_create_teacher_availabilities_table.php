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
        Schema::create('teacher_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('day_of_week'); // monday, tuesday, wednesday, thursday, friday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true); // true = disponible, false = no disponible
            $table->string('preference')->nullable(); // 'preferred', 'neutral', 'avoid'
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['teacher_id', 'day_of_week']);
            $table->index(['teacher_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_availabilities');
    }
};
