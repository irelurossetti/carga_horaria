<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1); // Cantidad de recursos asignados
            $table->text('notes')->nullable(); // Notas específicas del recurso en esta reserva
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['reservation_id', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_resources');
    }
};
