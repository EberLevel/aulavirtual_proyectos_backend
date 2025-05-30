<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->integer('plazo_os')->nullable(); // Plazo de O/S, numérico y opcional
            $table->string('vinculo')->nullable()->default(null); // Vínculo, texto y opcional
        });
    }

    public function down()
    {
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->dropColumn('plazo_os');
            $table->dropColumn('vinculo');
        });
    }
};
