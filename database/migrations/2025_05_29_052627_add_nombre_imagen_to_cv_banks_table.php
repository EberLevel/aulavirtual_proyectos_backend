<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreImagenToCvBanksTable extends Migration
{
    public function up()
    {
        Schema::table('cv_banks', function (Blueprint $table) {
            $table->string('nombre_imagen')->nullable()->after('image'); // Add nombre_imagen column
        });
    }

    public function down()
    {
        Schema::table('cv_banks', function (Blueprint $table) {
            $table->dropColumn('nombre_imagen');
        });
    }
}