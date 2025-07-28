<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToOfertasEmpleoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ofertas_empleo', function (Blueprint $table) {
            $table->string('url', 500)->nullable()->after('nombre_puesto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ofertas_empleo', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
}