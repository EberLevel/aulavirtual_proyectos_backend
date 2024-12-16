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
