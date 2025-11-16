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
        Schema::table('incidents', function (Blueprint $table) {
            if (!Schema::hasColumn('incidents', 'type')) {
                $table->string('type')->default('equipment')->after('room_id'); // equipment, infrastructure, cleaning, other
            }
            if (!Schema::hasColumn('incidents', 'priority')) {
                $table->string('priority')->default('medium')->after('type'); // low, medium, high, urgent
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['type', 'priority']);
        });
    }
};
