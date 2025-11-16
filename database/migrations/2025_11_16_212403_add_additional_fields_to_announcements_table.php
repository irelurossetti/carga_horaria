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
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'priority')) {
                $table->string('priority')->default('normal')->after('body'); // low, normal, high
            }
            if (!Schema::hasColumn('announcements', 'target')) {
                $table->string('target')->default('all')->after('priority'); // all, teachers, students
            }
            if (!Schema::hasColumn('announcements', 'active')) {
                $table->boolean('active')->default(true)->after('target');
            }
            if (!Schema::hasColumn('announcements', 'views')) {
                $table->integer('views')->default(0)->after('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['priority', 'target', 'active', 'views']);
        });
    }
};
