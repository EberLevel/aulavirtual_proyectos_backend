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
        Schema::create('reunion_fotos', function (Blueprint $table) {
            $table->id(); // ID autoincrementable para cada foto
            $table->unsignedBigInteger('reunion_id'); // Relación con la tabla 'reuniones'
            $table->text('foto'); // Aquí se almacena la cadena Base64 de la foto
            $table->timestamps(); // Añade created_at y updated_at

            // Definimos la relación con la tabla 'reuniones'
            $table->foreign('reunion_id')->references('id')->on('reuniones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reunion_fotos');
    }
};
