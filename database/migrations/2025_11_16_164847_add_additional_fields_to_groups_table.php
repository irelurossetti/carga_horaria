<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (!Schema::hasColumn('groups', 'enrolled_students')) {
                $table->integer('enrolled_students')->default(0)->after('capacity');
            }
            if (!Schema::hasColumn('groups', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('enrolled_students');
            }
            if (!Schema::hasColumn('groups', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('groups', 'room_id')) {
                $table->integer('room_id')->nullable()->after('schedule');
            }
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $columns = ['enrolled_students', 'status', 'description', 'room_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('groups', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
