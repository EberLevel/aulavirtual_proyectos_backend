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
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->unsignedBigInteger('cv_id')->nullable();
            $table->foreign('cv_id')->references('id')->on('cv_banks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas_puestos', function (Blueprint $table) {
            //
        });
    }
};
