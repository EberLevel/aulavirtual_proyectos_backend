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
        Schema::create('reuniones', function (Blueprint $table) {
            $table->id(); // Agrega un campo ID auto-incremental
            $table->string('titulo');
            $table->string('estado');
            $table->text('objetivo')->nullable(); // Asume que el campo puede ser nulo
            $table->text('resultado')->nullable(); // Asume que el campo puede ser nulo
            $table->unsignedBigInteger('domain_id'); // Relación con la tabla domains
            $table->timestamps(); // Agrega campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reuniones');
    }
};
