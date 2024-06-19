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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('duration');
            $table->decimal('price', 8, 2);
            $table->unsignedBigInteger('barbers_id');
            $table->timestamps();

            $table->foreign('barbers_id')->references('id')->on('barbers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['barbers_id']);
        });

        Schema::dropIfExists('services');
    }
};
