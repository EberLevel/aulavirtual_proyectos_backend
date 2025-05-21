<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('area_puestos');
        Schema::create('area_puestos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->string('codigo');
            $table->string('nombre');
            $table->string('telefono');
            $table->string('email');
            $table->decimal('salario_minimo', 10, 2);
            $table->decimal('salario_maximo', 10, 2);   
            $table->decimal('diferencia', 10, 2);
            $table->string('nivel');
            $table->string('dependencia');
            $table->string('nivel_perfil');
            $table->decimal('sueldo_promedio', 10, 2);
            $table->text('formacion');
            $table->text('capacitacion');
            $table->text('experiencia_especifica');
            $table->text('experiencia_general');
            $table->string('modalidad_posicion');
            $table->string('estado');
            $table->date('fecha_nombramiento');
            $table->string('nombre_nombrado');
            $table->foreign('area_id')->references('id')->on('institucion_area');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_puestos');
    }
};
