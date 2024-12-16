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
        Schema::create('puesto_perfiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('puesto_id');
            $table->string('codigo');
            $table->string('nombre');
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
            $table->foreign('puesto_id')->references('id')->on('area_puestos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puesto_perfiles');
    }
};
