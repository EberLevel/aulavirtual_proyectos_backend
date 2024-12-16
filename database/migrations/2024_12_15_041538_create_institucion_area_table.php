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
        Schema::create('institucion_area', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institucion_id');
            $table->unsignedBigInteger('area_padre_id')->nullable();
            $table->string('codigo');
            $table->string('nombre');
            $table->string('siglas');
            $table->string('telefono');
            $table->string('email');
            $table->text('direccion');
            $table->text('ubigeo');
            $table->foreign('institucion_id')->references('id')->on('instituciones');
            $table->foreign('area_padre_id')->references('id')->on('institucion_area');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institucion_area');
    }
};
