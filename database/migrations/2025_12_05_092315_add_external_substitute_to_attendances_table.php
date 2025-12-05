<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('external_substitute_name')->nullable()->after('substitute_teacher_id');
            $table->string('external_substitute_email')->nullable()->after('external_substitute_name');
            $table->string('external_substitute_phone')->nullable()->after('external_substitute_email');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['external_substitute_name', 'external_substitute_email', 'external_substitute_phone']);
        });
    }
};
