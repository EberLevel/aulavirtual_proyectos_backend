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
        Schema::create('puesto_continuidad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 191);
            $table->timestamps();
        });
        Schema::create('puesto_permanencia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 191);
            $table->timestamps();
        });
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->unsignedBigInteger('continuidad_id')->nullable();
            $table->foreign('continuidad_id')->references('id')->on('puesto_continuidad');
            $table->unsignedBigInteger('permanencia_id')->nullable();
            $table->foreign('permanencia_id')->references('id')->on('puesto_permanencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puesto_continuidad');
    }
};
