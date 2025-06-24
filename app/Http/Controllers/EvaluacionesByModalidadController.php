<?php

namespace App\Http\Controllers;

use App\Models\Evaluaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class EvaluacionesByModalidadController extends Controller
{
    /**
     * Obtener los alumnos inscritos a un curso a partir del id de una evaluación.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerAlumnosPorEvaluacion($id)
    {
        // Validar el id de la evaluación
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:evaluaciones,id',
        ]);

        // Si la validación falla, retornar un error
        if ($validator->fails()) {
            return response()->json([
                'message' => 'El ID de la evaluación no es válido o no existe.',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Obtener la evaluación con el curso y los alumnos inscritos
        // $evaluacion = Evaluaciones::with(['grupoDeEvaluacion.curso.alumnos' => function ($query) use ($id) {
        //     $query->leftJoin('evaluaciones_alumno', function($join) use ($id) {
        //         $join->on('evaluaciones_alumno.alumno_id', '=', 'alumnos.id')
        //              ->where('evaluaciones_alumno.evaluacion_id', '=', $id);
        //     })
        //     ->select('alumnos.*', 'evaluaciones_alumno.nota', 'evaluaciones_alumno.asistencia', 'evaluaciones_alumno.evaluacion_id');
        // }])->find($id);

        $evaluacionesAlumnos = DB::table('evaluaciones_alumno as ea')
        ->join('alumnos as a', 'ea.alumno_id', '=', 'a.id')
        ->join('evaluaciones as e', 'ea.evaluacion_id', '=', 'e.id')
        ->join('grupo_de_evaluaciones as ge', 'e.grupo_de_evaluaciones_id', '=', 'ge.id')
        ->join('curso_alumno as ca', 'ca.alumno_id', '=', 'a.id')
        ->select(
            'a.*',
            'ea.nota',
            'ea.asistencia',
            'ea.evaluacion_id',
            'e.tipo_evaluacion_id'
        )
        ->where('ea.evaluacion_id', $id)
        ->where('ca.estado_id', 2)
        ->get();




        // Retornar los datos de los alumnos en formato JSON
        return response()->json($evaluacionesAlumnos);
    }


    /**
     * Guardar las notas de los alumnos para una evaluación.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarNotas(Request $request)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'evaluacion_id' => 'required|integer|exists:evaluaciones,id',
            'notas' => 'required|array',
            'notas.*.alumno_id' => 'required|integer|exists:alumnos,id',
            'notas.*.nota' => 'required|numeric|min:0|max:20', // Ajusta el rango según tu sistema de notas
            'notas.*.asistencia' => 'required|numeric'
        ]);

        // dd($request->all());

        // Si la validación falla, devolver errores
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en los datos enviados',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Obtener los datos de la solicitud
        $evaluacionId = $request->input('evaluacion_id');
        $notas = $request->input('notas');

        // Procesar y guardar las notas
        foreach ($notas as $nota) {
            DB::table('evaluaciones_alumno')
                ->updateOrInsert(
                    ['evaluacion_id' => $evaluacionId, 'alumno_id' => $nota['alumno_id']],
                    [
                        'nota' => $nota['nota'],
                        'asistencia' => $nota['asistencia']
                    ]
                );
        }

        return response()->json(['message' => 'Notas guardadas correctamente']);
    }
}
