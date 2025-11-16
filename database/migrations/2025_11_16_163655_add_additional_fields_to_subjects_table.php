<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'semester')) {
                $table->integer('semester')->nullable()->after('code');
            }
            if (!Schema::hasColumn('subjects', 'theoretical_hours')) {
                $table->integer('theoretical_hours')->default(0)->after('semester');
            }
            if (!Schema::hasColumn('subjects', 'practical_hours')) {
                $table->integer('practical_hours')->default(0)->after('theoretical_hours');
            }
            if (!Schema::hasColumn('subjects', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('subjects', 'prerequisites')) {
                $table->string('prerequisites')->nullable()->after('practical_hours');
            }
            if (!Schema::hasColumn('subjects', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('prerequisites');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $columns = ['semester', 'theoretical_hours', 'practical_hours', 'description', 'prerequisites', 'status'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('subjects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
