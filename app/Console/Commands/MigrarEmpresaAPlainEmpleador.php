<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrarEmpresaAPlainEmpleador extends Command
{
    protected $signature = 'migrar:empleador-plain';
    protected $description = 'Copia los datos de empresa a plain_empleador en ofertas_empleo';

    public function handle()
    {
        $total = DB::table('ofertas_empleo')->whereNotNull('empresa')->count();

        DB::table('ofertas_empleo')
            ->whereNotNull('empresa')
            ->update([
                'plain_empleador' => DB::raw('empresa')
            ]);

        $this->info("Migración completada. Registros actualizados: {$total}");
    }
}