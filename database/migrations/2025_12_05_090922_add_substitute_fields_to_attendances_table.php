<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_substitute')->default(false)->after('status');
            $table->unsignedBigInteger('original_teacher_id')->nullable()->after('is_substitute');
            $table->unsignedBigInteger('substitute_teacher_id')->nullable()->after('original_teacher_id');
            
            // Índices para mejorar búsquedas
            $table->index('is_substitute');
            $table->index('original_teacher_id');
            $table->index('substitute_teacher_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['is_substitute']);
            $table->dropIndex(['original_teacher_id']);
            $table->dropIndex(['substitute_teacher_id']);
            $table->dropColumn(['is_substitute', 'original_teacher_id', 'substitute_teacher_id']);
        });
    }
};
