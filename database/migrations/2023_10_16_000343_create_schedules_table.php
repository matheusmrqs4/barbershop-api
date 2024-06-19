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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->timestamp('schedule');
            $table->unsignedBigInteger('services_id');
            $table->unsignedBigInteger('barbers_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('services_id')->references('id')->on('services');
            $table->foreign('barbers_id')->references('id')->on('barbers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['services_id']);
            $table->dropForeign(['barbers_id']);
        });

        Schema::dropIfExists('schedules');
    }
};
