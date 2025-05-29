<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('capacitacion_postulante', function (Blueprint $table) {
            $table->integer('nro_pagina_cv')->nullable()->after('observaciones'); // Add nro_pagina_cv as nullable integer
            $table->boolean('validado')->default(false)->after('nro_pagina_cv'); // Add validado after nro_pagina_cv
        });
    }

    public function down()
    {
        Schema::table('capacitacion_postulante', function (Blueprint $table) {
            $table->dropColumn(['nro_pagina_cv', 'validado']);
        });
    }
};
