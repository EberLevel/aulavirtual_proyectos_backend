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
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->longText('perfil')->nullable();
            $table->longText('observaciones')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->integer('dias_restantes')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_puestos', function (Blueprint $table) {
            //
        });
    }
};
