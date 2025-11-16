<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    
    public function up(): void
    {
        Schema::table('teacher_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_assignments', 'horas_semanales')) {
                $table->integer('horas_semanales')->nullable()->after('period_id');
            }
            if (!Schema::hasColumn('teacher_assignments', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->after('horas_semanales');
            }
            if (!Schema::hasColumn('teacher_assignments', 'fecha_fin')) {
                $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            }
            if (!Schema::hasColumn('teacher_assignments', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('fecha_fin');
            }
            if (!Schema::hasColumn('teacher_assignments', 'tipo_asignacion')) {
                $table->string('tipo_asignacion')->nullable()->after('observaciones');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $columns = ['horas_semanales', 'fecha_inicio', 'fecha_fin', 'observaciones', 'tipo_asignacion'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('teacher_assignments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
