<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: 'Parcial 1', 'Examen Final', 'Proyecto'
            $table->decimal('weight', 5, 2); // Peso en porcentaje (0-100)
            $table->unsignedBigInteger('period_id'); // Periodo académico
            $table->unsignedBigInteger('subject_id')->nullable(); // Materia específica (opcional)
            $table->unsignedBigInteger('group_id')->nullable(); // Grupo específico (opcional)
            $table->text('description')->nullable();
            $table->date('evaluation_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índices
            $table->index('period_id');
            $table->index('subject_id');
            $table->index('group_id');
            
            // Claves foráneas
            $table->foreign('period_id')->references('id')->on('academic_periods')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria');
    }
};
