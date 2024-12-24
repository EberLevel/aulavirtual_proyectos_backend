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
        Schema::create('puestos_estado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 191);
            $table->timestamps();

        });
        //insertar datos
        DB::table('puestos_estado')->insert([
            ['id' => 1, 'nombre' => 'Ocupado'],
            ['id' => 2, 'nombre' => 'Disponible'],
            ['id' => 3, 'nombre' => 'Congelado'],
        ]);
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
