<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del recurso
            $table->string('serial_number')->unique()->nullable(); // Número de serie
            $table->enum('type', ['Proyector', 'Laptop', 'Pizarra Digital', 'Micrófono', 'Parlantes', 'Otro'])->default('Otro');
            $table->enum('status', ['Disponible', 'En Uso', 'Mantenimiento', 'Dañado'])->default('Disponible');
            $table->text('description')->nullable(); // Descripción adicional
            $table->string('brand')->nullable(); // Marca
            $table->string('model')->nullable(); // Modelo
            $table->string('location')->nullable(); // Ubicación física
            $table->date('purchase_date')->nullable(); // Fecha de compra
            $table->timestamps();
            $table->softDeletes(); // Para eliminación lógica
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
