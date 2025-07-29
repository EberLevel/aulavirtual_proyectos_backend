<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOfertasEmpleoEmpleadorId extends Migration
{
    public function up()
    {
        Schema::table('ofertas_empleo', function (Blueprint $table) {
            // Eliminar empresa_id y plain_empresa si existen
            if (Schema::hasColumn('ofertas_empleo', 'empresa_id')) {
                $table->dropColumn('empresa_id');
            }
            if (Schema::hasColumn('ofertas_empleo', 'plain_empresa')) {
                $table->dropColumn('plain_empresa');
            }
            // Agregar plain_empleador si no existe
            if (!Schema::hasColumn('ofertas_empleo', 'plain_empleador')) {
                $table->string('plain_empleador')->nullable()->after('empleador_id');
            }
            // Agregar la foreign key (solo si no existe, si ya existe puedes comentar la siguiente línea)
            // $table->foreign('empleador_id')->references('id')->on('empleadores');
        });
    }

    public function down()
    {
        Schema::table('ofertas_empleo', function (Blueprint $table) {
            // Restaurar empresa_id y plain_empresa si es necesario
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->string('plain_empresa')->nullable();

            // Eliminar empleador_id y plain_empleador
            $table->dropForeign(['empleador_id']);
            $table->dropColumn('empleador_id');
            $table->dropColumn('plain_empleador');
        });
    }
}