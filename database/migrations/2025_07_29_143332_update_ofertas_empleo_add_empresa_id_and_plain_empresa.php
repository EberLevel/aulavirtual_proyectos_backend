<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOfertasEmpleoAddEmpresaIdAndPlainEmpresa extends Migration
{
    public function up()
    {
        Schema::table('ofertas_empleo', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_id')->nullable()->after('estado');
            $table->string('plain_empresa')->nullable()->after('empresa_id');
            // Si quieres eliminar el campo antiguo 'empresa', descomenta la siguiente línea:
            // $table->dropColumn('empresa');
        });
    }

    public function down()
    {
        Schema::table('ofertas_empleo', function (Blueprint $table) {
            $table->dropColumn('empresa_id');
            $table->dropColumn('plain_empresa');
            // Si eliminaste el campo 'empresa', puedes restaurarlo así:
            // $table->string('empresa')->nullable();
        });
    }
}