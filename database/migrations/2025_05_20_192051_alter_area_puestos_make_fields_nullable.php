<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAreaPuestosMakeFieldsNullable extends Migration
{
    public function up()
    {
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->string('telefono', 191)->nullable()->change();
            $table->string('email', 191)->nullable()->change();
            $table->decimal('salario_minimo', 10, 2)->nullable()->change();
            $table->decimal('salario_maximo', 10, 2)->nullable()->change();
            $table->decimal('diferencia', 10, 2)->nullable()->change();
            $table->string('nivel', 191)->nullable()->change();
            $table->string('dependencia', 191)->nullable()->change();
            $table->string('nivel_perfil', 191)->nullable()->change();
            $table->decimal('sueldo_promedio', 10, 2)->nullable()->change();
            $table->text('formacion')->nullable()->change();
            $table->text('capacitacion')->nullable()->change();
            $table->text('experiencia_especifica')->nullable()->change();
            $table->text('experiencia_general')->nullable()->change();
            $table->string('estado', 191)->nullable()->change();
            $table->date('fecha_nombramiento')->nullable()->change();
            $table->string('nombre_nombrado', 191)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->string('telefono', 191)->nullable(false)->change();
            $table->string('email', 191)->nullable(false)->change();
            $table->decimal('salario_minimo', 10, 2)->nullable(false)->change();
            $table->decimal('salario_maximo', 10, 2)->nullable(false)->change();
            $table->decimal('diferencia', 10, 2)->nullable(false)->change();
            $table->string('nivel', 191)->nullable(false)->change();
            $table->string('dependencia', 191)->nullable(false)->change();
            $table->string('nivel_perfil', 191)->nullable(false)->change();
            $table->decimal('sueldo_promedio', 10, 2)->nullable(false)->change();
            $table->text('formacion')->nullable(false)->change();
            $table->text('capacitacion')->nullable(false)->change();
            $table->text('experiencia_especifica')->nullable(false)->change();
            $table->text('experiencia_general')->nullable(false)->change();
            $table->string('estado', 191)->nullable(false)->change();
            $table->date('fecha_nombramiento')->nullable(false)->change();
            $table->string('nombre_nombrado', 191)->nullable(false)->change();
        });
    }
}