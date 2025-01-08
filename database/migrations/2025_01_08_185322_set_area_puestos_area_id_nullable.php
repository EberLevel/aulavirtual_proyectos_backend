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
       //modify the area_id column in the area_puestos table to be nullable
         Schema::table('area_puestos', function (Blueprint $table) {
                $table->unsignedBigInteger('area_id')->nullable()->change();
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
