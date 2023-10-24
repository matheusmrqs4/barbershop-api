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
        Schema::table('barber_shops', function (Blueprint $table) {
            $table->string('bio')->nullable()->after('address3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barber_shops', function (Blueprint $table) {
            $table->dropColumn('bio');
        });
    }
};
