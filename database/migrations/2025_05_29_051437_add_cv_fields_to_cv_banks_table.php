<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cv_banks', function (Blueprint $table) {
            $table->string('cv_path')->nullable()->after('image'); // Path to CV file
            $table->string('nombre_cv')->nullable()->after('cv_path'); // CV file name
        });
    }

    public function down()
    {
        Schema::table('cv_banks', function (Blueprint $table) {
            $table->dropColumn(['cv_path', 'nombre_cv']);
        });
    }
};
