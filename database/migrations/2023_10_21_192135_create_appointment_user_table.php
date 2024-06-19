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
        Schema::create('appointment_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointments_id');
            $table->unsignedBigInteger('users_id');
            $table->timestamps();

            $table->foreign('appointments_id')->references('id')->on('appointments');
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_user', function (Blueprint $table) {
            $table->dropForeign(['appointments_id']);
            $table->dropForeign(['users_id']);
        });

        Schema::dropIfExists('appointment_user');
    }
};
