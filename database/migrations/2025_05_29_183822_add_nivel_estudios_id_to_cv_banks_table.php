<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
/**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cv_banks', function (Blueprint $table) {
            $table->unsignedBigInteger('nivel_estudios_id')->nullable()->after('color_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cv_banks', function (Blueprint $table) {
            $table->dropColumn('nivel_estudios_id');
        });
    }
};
