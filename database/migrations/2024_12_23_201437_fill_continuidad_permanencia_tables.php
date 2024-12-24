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
        /*
        * continuidadLista: any[] = [
    {
      id: 1,
      nombre: 'Continuo'
    },
    {
      id: 2,
      nombre: 'Observado'
    },
    {
      id:3,
      nombre:'Finlizar'
    }
  ];
  permanenciaLista: any[] = [{
    id:1,
    nombre: 'Nombrado'
  },
  {
    id:2,
    nombre: 'Indefinido'
  },
  {
    id:3,
    nombre: 'Limitado'
  }        
        */
        DB::table('puesto_continuidad')->insert([
            ['id' => 1, 'nombre' => 'Continuo'],
            ['id' => 2, 'nombre' => 'Observado'],
            ['id' => 3, 'nombre' => 'Finlizar']
        ]);
        DB::table('puesto_permanencia')->insert([
            ['id' => 1, 'nombre' => 'Nombrado'],
            ['id' => 2, 'nombre' => 'Indefinido'],
            ['id' => 3, 'nombre' => 'Limitado']
        ]);
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
