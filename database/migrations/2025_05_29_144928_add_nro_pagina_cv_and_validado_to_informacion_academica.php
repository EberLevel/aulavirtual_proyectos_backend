<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNroPaginaCvAndValidadoToInformacionAcademica extends Migration
{
    public function up()
    {
        Schema::table('informacion_academica', function (Blueprint $table) {
            $table->integer('nro_pagina_cv')->nullable()->after('observaciones');
            $table->boolean('validado')->default(false)->after('nro_pagina_cv');
            $table->string('imagen_certificado')->nullable()->change(); // Asegura que la columna sea nullable
        });
    }

    public function down()
    {
        Schema::table('informacion_academica', function (Blueprint $table) {
            $table->dropColumn('nro_pagina_cv');
            $table->dropColumn('validado');
        });
    }
}