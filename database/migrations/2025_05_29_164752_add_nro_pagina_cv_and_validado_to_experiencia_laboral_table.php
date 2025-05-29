<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('experiencia_laboral', function (Blueprint $table) {
            $table->integer('nro_pagina_cv')->nullable()->after('imagen');
            $table->boolean('validado')->default(0)->after('nro_pagina_cv');
        });
    }

    public function down()
    {
        Schema::table('experiencia_laboral', function (Blueprint $table) {
            $table->dropColumn('nro_pagina_cv');
            $table->dropColumn('validado');
        });
    }
};
