<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Usuario con rol ESTUDIANTE
            $table->unsignedBigInteger('evaluation_criteria_id');
            $table->decimal('score', 5, 2); // Nota (0-100)
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable(); // Docente que calificó
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index('student_id');
            $table->index('evaluation_criteria_id');
            $table->index('graded_by');
            
            // Claves foráneas
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('evaluation_criteria_id')->references('id')->on('evaluation_criteria')->onDelete('cascade');
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
            
            // Evitar duplicados: un estudiante solo puede tener una nota por criterio
            $table->unique(['student_id', 'evaluation_criteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
