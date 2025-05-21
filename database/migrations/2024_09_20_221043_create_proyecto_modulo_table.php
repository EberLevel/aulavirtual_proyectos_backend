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
        Schema::create('proyecto_modulo', function (Blueprint $table) {
            $table->id(); // Llave primaria
            $table->string('nombre');
            $table->integer('prioridad')->nullable();
            $table->string('estado')->default('activo');
            $table->string('grupo')->nullable();
            $table->string('responsable')->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('proyecto_id'); // Llave foránea

            // Relación con la tabla proyectos
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');

            $table->timestamps(); // Para las columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_modulo');
    }
};
