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
        //check if column exists and if not create it else drop it and create it
      Schema::table('permiso', function (Blueprint $table) {
            $table->integer('permiso_parent_id')->nullable();
            $table->enum('tipo', ['menu', 'submenu', 'action'])->default('menu');
            $table->foreign('permiso_parent_id')->references('id')->on('permiso');
        });
        
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permiso', function (Blueprint $table) {
            //
        });
    }
};
