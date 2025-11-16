<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_periods', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_periods', 'code')) {
                $table->string('code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('academic_periods', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });
        
        // Generar códigos para registros existentes que no tengan código
        $periods = DB::table('academic_periods')->whereNull('code')->get();
        foreach ($periods as $period) {
            $year = date('Y', strtotime($period->start_date ?? 'now'));
            $semester = date('n', strtotime($period->start_date ?? 'now')) <= 6 ? '1' : '2';
            $code = "PER-{$year}{$semester}";
            
            // Si el código ya existe, agregar un sufijo
            $counter = 1;
            $originalCode = $code;
            while (DB::table('academic_periods')->where('code', $code)->exists()) {
                $code = $originalCode . '-' . $counter;
                $counter++;
            }
            
            DB::table('academic_periods')
                ->where('id', $period->id)
                ->update(['code' => $code]);
        }
        
        // Ahora hacer la columna NOT NULL y UNIQUE
        Schema::table('academic_periods', function (Blueprint $table) {
            $table->string('code')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('academic_periods', function (Blueprint $table) {
            if (Schema::hasColumn('academic_periods', 'code')) {
                $table->dropColumn('code');
            }
            if (Schema::hasColumn('academic_periods', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
