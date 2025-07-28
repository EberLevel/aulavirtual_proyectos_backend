<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoActualPostulanteIdAndAnoIdToCvBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cv_banks', function (Blueprint $table) {
            // Agregar campo para estado actual del postulante
            $table->unsignedBigInteger('estado_actual_postulante_id')->nullable()->after('estado_actual_id');
            
            // Agregar campo para año
            $table->unsignedBigInteger('ano_id')->nullable()->after('domain_id');
            
            // Agregar foreign keys si las tablas existen
            $table->foreign('estado_actual_postulante_id')->references('id')->on('estado_actual')->onDelete('set null');
            $table->foreign('ano_id')->references('id')->on('ano')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cv_banks', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['estado_actual_postulante_id']);
            $table->dropForeign(['ano_id']);
            
            // Eliminar columnas
            $table->dropColumn(['estado_actual_postulante_id', 'ano_id']);
        });
    }
}