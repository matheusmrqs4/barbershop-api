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
        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('barbers_id')->after('users_id');
            $table->unsignedBigInteger('services_id')->after('users_id');

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
            $table->dropForeign(['barbers_id']);
            $table->dropForeign(['services_id']);

            $table->dropColumn('barbers_id');
            $table->dropColumn('services_id');
        });
    }
};
