<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipanteController extends Controller
{
    public function show($domain_id, $curso_id)
    {
        $participantes = DB::table('alumnos as a')
            ->select(
                'a.codigo',
                'a.estadoAlumno',
                'a.id',
                'ca.id as curso_alumno_id',
                DB::raw("CONCAT(a.nombres, ' ', a.apellidos) as nombres"),
                'a.celular',
                'a.email',
                'a.dni',
                'ca.curso_id',
                'ca.estado_id',
                'ca.estado_curso_id',
                'ec.nombre as estado_alumno_curso',
                'ec.color',
                'ca.domain_id'
            )
            ->join('curso_alumno as ca', function ($join) use ($curso_id, $domain_id) {
                $join->on('ca.alumno_id', '=', 'a.id')
                    ->where('ca.curso_id', $curso_id)
                    ->where('ca.domain_id', $domain_id)
                    ->where('ca.estado_id', 2);
            })
            ->join('estado_curso_alumno as ec', 'ec.id', '=', 'ca.estado_curso_id')
            ->where('a.domain_id', $domain_id)
            ->get();

        return response()->json($participantes);
        // return response()->json("Jorge");
    }




    public function store(Request $request)
    {
        $domainId = $request->input('domain_id');
        $cursoId = $request->input('curso_id');
        $alumnoId = $request->input('alumno_id');
        //verificar si el alumno ya esta registrado en el curso
        $cursoAlumno = DB::table('curso_alumno')
            ->where('curso_id', $cursoId)
            ->where('alumno_id', $alumnoId)
            ->where('domain_id', $domainId)
            ->first();
        if ($cursoAlumno) {
            //remover el registro
            DB::table('curso_alumno')->where('id', $cursoAlumno->id)->delete();
        } else {
            //insertar el registro
            DB::table('curso_alumno')->insert([
                'curso_id' => $cursoId,
                'alumno_id' => $alumnoId,
                'domain_id' => $domainId
            ]);
        }
    }
    public function getPromedioEvaluaciones($curso_id, $alumno_id)
    {
        // Realizar la consulta para obtener el promedio general de las evaluaciones agrupadas por grupo de evaluaciones
        $promedioGeneral = DB::table(DB::raw('(SELECT ge.id AS grupo_id, AVG(IFNULL(ea.nota, 0)) AS promedio_notas_por_grupo
            FROM grupo_de_evaluaciones ge
            LEFT JOIN evaluaciones e ON e.grupo_de_evaluaciones_id = ge.id
            LEFT JOIN evaluaciones_alumno ea ON ea.evaluacion_id = e.id AND ea.alumno_id = ?
            WHERE ge.curso_id = ?
            GROUP BY ge.id) AS subquery'))
            ->select(DB::raw('AVG(subquery.promedio_notas_por_grupo) AS promedio_general'))
            ->setBindings([$alumno_id, $curso_id])
            ->first();

        // Devolver el resultado en formato JSON
        return response()->json([
            'curso_id' => $curso_id,
            'alumno_id' => $alumno_id,
            'promedio_general' => round($promedioGeneral->promedio_general, 2) // Redondea a 2 decimales
        ]);
    }


    public function getPromedioCurso($curso_id)
    {
        // Obtener los IDs de los alumnos inscritos en el curso
        $alumnos = DB::table('curso_alumno')
            ->where('curso_id', $curso_id)
            ->pluck('alumno_id');

        // Inicializar la suma de los promedios
        $sumaPromedios = 0;

        // Recorrer cada alumno y calcular su promedio
        foreach ($alumnos as $alumno_id) {
            $promedioAlumno = DB::table(DB::raw('(SELECT ge.id AS grupo_id, AVG(IFNULL(ea.nota, 0)) AS promedio_notas_por_grupo
                FROM grupo_de_evaluaciones ge
                LEFT JOIN evaluaciones e ON e.grupo_de_evaluaciones_id = ge.id
                LEFT JOIN evaluaciones_alumno ea ON ea.evaluacion_id = e.id AND ea.alumno_id = ?
                WHERE ge.curso_id = ?
                GROUP BY ge.id) AS subquery'))
                ->select(DB::raw('AVG(subquery.promedio_notas_por_grupo) AS promedio_general'))
                ->setBindings([$alumno_id, $curso_id])
                ->first();

            // Sumar el promedio del alumno al total
            $sumaPromedios += $promedioAlumno->promedio_general;
        }

        // Calcular el promedio general del curso
        $promedioGeneralCurso = $sumaPromedios / max(count($alumnos), 1); // Evitar división por 0

        // Devolver el resultado en formato JSON
        return response()->json([
            'curso_id' => $curso_id,
            'promedio_general' => round($promedioGeneralCurso, 2) // Redondear a 2 decimales
        ]);
    }

    public function updateEstadoCursoAlumno(Request $request)
    {

        $domainId   = $request->input('domain_id');
        $alumnoId   = $request->input('id_alumno');
        $cursoId    = $request->input('curso_id');
        $estadoId   = $request->input('id_estado');

        DB::table('curso_alumno')
            ->where('alumno_id', $alumnoId)
            ->where('curso_id', $cursoId)
            ->where('domain_id', $domainId)
            ->update([
                'estado_curso_id' => $estadoId
            ]);
        return response()->json([
            'message' => 'Estado actualizado correctamente.',
        ], 201);
    }
}
