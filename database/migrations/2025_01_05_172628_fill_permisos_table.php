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
        //add column key to permiso table
        Schema::table('permiso', function (Blueprint $table) {
            $table->string('key')->nullable();
        });
        /**
         * insert into permiso(nombre)
          *  values("multi-empresas"),
          *  ("seguridad"),
          *  ("estructura-organica"),
           * ("aula-virtual"),
           * ("agenda-virtual"),
           * ("ejecucion-proyectos"),
           * ("gestion-incidencias"),
           * ("proyectos");
         */
        //truncate permiso table
        DB::table('permiso')->insert([
            [ 'nombre'=>'MULTI EMPRESAS',
                'key' => 'multi-empresas'],
            [ 'nombre'=>'SEGURIDAD',
                'key' => 'seguridad'],
            [ 'nombre'=>'ESTRUCTURA ORGANICA',
                'key' => 'estructura-organica'],
            [ 'nombre'=>'AULA VIRTUAL',
                'key' => 'aula-virtual'],
            [ 'nombre'=>'AGENDA VIRTUAL',
                'key' => 'agenda-virtual'],
            [ 'nombre'=>'EJECUCION PROYECTOS',
                'key' => 'ejecucion-proyectos'],
            [ 'nombre'=>'GESTION INCIDENCIAS',
                'key' => 'gestion-incidencias'],
            [ 'nombre'=>'PROYECTOS',
                'key' => 'proyectos'],
        ]);
        //find permiso with nombre = 'multi-empresas'
        $permisoMulti = DB::table('permiso')->where('key', 'multi-empresas')->first();
        if($permisoMulti){
            //insert into permiso(nombre, permiso_parent_id, tipo)
            //values('multi-empresas', null, 'menu')
            DB::table('permiso')->insert([
                'key' => 'empresas',
                'nombre' => 'Empresas',
                'permiso_parent_id' => $permisoMulti->id,
                'tipo' => 'submenu'
            ]);
        }
        $permisoSeguridad = DB::table('permiso')->where('key', 'seguridad')->first();
        if($permisoSeguridad){
            DB::table('permiso')->insert([
                'nombre' => 'Configuración',
                'key' => 'configuracion',
                'permiso_parent_id' => $permisoSeguridad->id,
                'tipo' => 'submenu'
            ]);
            DB::table('permiso')->insert([
                'nombre' => 'Roles y permisos',
                'key' => 'roles-permisos',
                'permiso_parent_id' => $permisoSeguridad->id,
                'tipo' => 'submenu'
            ]);
            DB::table('permiso')->insert([
                'nombre' => 'Usuarios',
                'key' => 'usuarios',
                'permiso_parent_id' => $permisoSeguridad->id,
                'tipo' => 'submenu'
            ]);
        }
        $permisoEstructura = DB::table('permiso')->where('key', 'estructura-organica')->first();
        if($permisoEstructura){
            //get inserted id
            
            $bancoCvId= DB::table('permiso')->insertGetId([
                'nombre' => 'Banco de CV',
                'key' => 'banco-cv',
                'permiso_parent_id' => $permisoEstructura->id,
                'tipo' => 'submenu'
            ]);
            
            if($bancoCvId){
                DB::table('permiso')->insert([
                    'nombre' => 'Egresados',
                    'key' => 'egresados',
                    'permiso_parent_id' => $bancoCvId,
                    'tipo' => 'submenu'
                ]);
               
            }
            DB::table('permiso')->insert([
                'nombre' =>'Instituciones',
                'key' => 'instituciones',
                'permiso_parent_id' => $permisoEstructura->id,
                'tipo' => 'submenu'
            ]);
            DB::table('permiso')->insert([
                'nombre' => 'Reporte Puestos',
                'key' => 'reporte-puestos',
                'permiso_parent_id' => $permisoEstructura->id,
                'tipo' => 'submenu'
            ]);
            $mantenimientoId = DB::table('permiso')->insertGetId([
                'nombre' => 'Mantenimiento',
                'key' => 'mantenimiento',
                'permiso_parent_id' => $permisoEstructura->id,
                'tipo' => 'submenu'
            ]);
            if($mantenimientoId){
                //año, gestiones,grado instruccion,vinculos laborales,niveles de puesto,modalidad de puesto,profesiones,estado de avance,escala,ocupacion actual,bolsa de trabajo
                DB::table('permiso')->insert([
                    'nombre' => 'Año',
                    'key' => 'anio',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Gestiones',
                    'key' => 'gestiones',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Grado Instrucción',
                    'key' => 'grado-instruccion',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Vinculos Laborales',
                    'key' => 'vinculos-laborales',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Niveles de Puesto',
                    'key' => 'niveles-puesto',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Modalidad de Puesto',
                    'key' => 'modalidad-puesto',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Profesiones',
                    'key' => 'profesiones',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Estado de Avance',
                    'key' => 'estado-avance',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Escala',
                    'key' => 'escala',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Ocupación Actual',
                    'key' => 'ocupacion-actual',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Bolsa de Trabajo',
                    'key' => 'bolsa-trabajo',
                    'permiso_parent_id' => $mantenimientoId,
                    'tipo' => 'submenu'
                ]);
                
            }
           
        }
        $aulaVirtualId = DB::table('permiso')->where('key', 'aula-virtual')->first();
        if($aulaVirtualId){
            $mantenimientoAulaId = DB::table('permiso')->insertGetId([
                'nombre' => 'Mantenimientos',
                'key' => 'mantenimiento-aula',
                'permiso_parent_id' => $aulaVirtualId->id,
                'tipo' => 'submenu'
            ]);
            if($mantenimientoAulaId){
               //modulo,competencia, area de promociones,estados,plan de estudio, ciclos ,aulas
                DB::table('permiso')->insert([
                    'nombre' => 'Modulo',
                    'key' => 'modulo',
                    'permiso_parent_id' => $mantenimientoAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Competencia',
                    'key' => 'competencia',
                    'permiso_parent_id' => $mantenimientoAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Area de Promociones',
                    'key' => 'area-promociones',
                    'permiso_parent_id' => $mantenimientoAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Estados',
                    'key' => 'estados',
                    'permiso_parent_id' => $mantenimientoAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Plan de Estudio',
                    'key' => 'plan-estudio',
                    'permiso_parent_id' => $mantenimientoAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Ciclos',
                    'key' => 'ciclos',
                    'permiso_parent_id' => $mantenimientoAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Aulas',
                    'key' => 'aulas',
                    'permiso_parent_id' => $mantenimientoAulaId,
                    'tipo' => 'submenu'
                ]);

            }
            //carreras tecnincas,unidades didacticas
            DB::table('permiso')->insert([
                'nombre' => 'Carreras Tecnicas',
                'key' => 'carreras-tecnicas',
                'permiso_parent_id' => $aulaVirtualId->id,
                'tipo' => 'submenu'
            ]);
            DB::table('permiso')->insert([
                'nombre' => 'Unidades Didacticas',
                'key' => 'unidades-didacticas',
                'permiso_parent_id' => $aulaVirtualId->id,
                'tipo' => 'submenu'
            ]);
            $alumnoAulaId = DB::table('permiso')->insertGetId([
                'nombre' => 'Alumnos',
                'key' => 'alumnos',
                'permiso_parent_id' => $aulaVirtualId->id,
                'tipo' => 'submenu'
            ]);
            if($alumnoAulaId){
               //datos personales, documentos de gestion, unidad didactica
                DB::table('permiso')->insert([
                    'nombre' => 'Datos Personales',
                    'key' => 'datos-personales-alumno',
                    'permiso_parent_id' => $alumnoAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Documentos de Gestion',
                    'key' => 'documentos-gestion',
                    'permiso_parent_id' => $alumnoAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Unidad Didactica',
                    'key' => 'unidad-didactica-alumno',
                    'permiso_parent_id' => $alumnoAulaId,
                    'tipo' => 'submenu'
                ]);

            }
            $docenteAulaId = DB::table('permiso')->insertGetId([
                'nombre' => 'Docentes',
                'key' => 'docentes',
                'permiso_parent_id' => $aulaVirtualId->id,
                'tipo' => 'submenu'
            ]);
            if($docenteAulaId){
               //datos personales, documentos de gestion, unidad didactica
                DB::table('permiso')->insert([
                    'nombre' => 'Datos Personales',
                    'key' => 'datos-personales-docente',
                    'permiso_parent_id' => $docenteAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Documentos de Gestion',
                    'key' => 'documentos-gestion-docente',
                    'permiso_parent_id' => $docenteAulaId,
                    'tipo' => 'submenu'
                ]);
                DB::table('permiso')->insert([
                    'nombre' => 'Unidad Didactica',
                    'key' => 'unidad-didactica-docente',
                    'permiso_parent_id' => $docenteAulaId,
                    'tipo' => 'submenu'
                ]);
            }
            //capacitaciones,pagos
            DB::table('permiso')->insert([
                'nombre' => 'Capacitaciones',
                'key' => 'capacitaciones',
                'permiso_parent_id' => $aulaVirtualId->id,
                'tipo' => 'submenu'
            ]);
            DB::table('permiso')->insert([
                'nombre' => 'Pagos',
                'key' => 'pagos',
                'permiso_parent_id' => $aulaVirtualId->id,
                'tipo' => 'submenu'
            ]);

        }
        $agendaVirtualId = DB::table('permiso')->where('key', 'agenda-virtual')->first();
        if($agendaVirtualId){
            //candidatos
            DB::table('permiso')->insert([
                'nombre' => 'Candidatos',
                'key' => 'candidatos',
                'permiso_parent_id' => $agendaVirtualId->id,
                'tipo' => 'submenu'
            ]);
        }
        $ejecucionProyectosId = DB::table('permiso')->where('key', 'ejecucion-proyectos')->first();
        if($ejecucionProyectosId){
//ejecucion
            DB::table('permiso')->insert([
                'nombre' => 'Ejecucion',
                'key' => 'ejecucion',
                'permiso_parent_id' => $ejecucionProyectosId->id,
                'tipo' => 'submenu'
            ]);
        }
        $gestionIncidenciasId = DB::table('permiso')->where('key', 'gestion-incidencias')->first();
        if($gestionIncidenciasId){
            //incidencias
            DB::table('permiso')->insert([
                'nombre' => 'Incidencias',
                'key' => 'incidencias',
                'permiso_parent_id' => $gestionIncidenciasId->id,
                'tipo' => 'submenu'
            ]);
        }
        $proyectosId = DB::table('permiso')->where('key', 'proyectos')->first();
        if($proyectosId){
            //proyectos
            DB::table('permiso')->insert([
                'nombre' => 'Proyectos',
                'key' => 'proyectos',
                'permiso_parent_id' => $proyectosId->id,
                'tipo' => 'submenu'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
