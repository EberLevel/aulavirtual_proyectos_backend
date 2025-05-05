<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->string('descripcion_servicio', 500)->nullable()->after('nombre');
        });
    }
    
    public function down(): void
    {
        Schema::table('area_puestos', function (Blueprint $table) {
            $table->dropColumn('descripcion_servicio');
        });
    }
    
};
