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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->timestamp('schedule_time');
            $table->unsignedBigInteger('schedules_id');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('barbers_id');
            $table->unsignedBigInteger('services_id');
            $table->timestamps();

            $table->foreign('schedules_id')->references('id')->on('schedules');
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('barbers_id')->references('id')->on('barbers');
            $table->foreign('services_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['schedules_id']);
            $table->dropForeign(['users_id']);
            $table->dropForeign(['barbers_id']);
            $table->dropForeign(['services_id']);
        });

        Schema::dropIfExists('appointments');
    }
};
