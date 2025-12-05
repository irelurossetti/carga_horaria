<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        if (! Schema::hasTable('reservations')) {
            Schema::create('reservations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('room_id');
                $table->unsignedBigInteger('schedule_id')->nullable();
                $table->unsignedBigInteger('teacher_id')->nullable(); // Nullable para admins
                $table->timestamp('reserved_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['room_id']);
                $table->index(['teacher_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
