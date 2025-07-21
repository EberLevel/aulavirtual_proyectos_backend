<?php

namespace App\Http\Controllers;

use App\Models\Evaluaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EvaluacionesByModalidadController extends Controller
{
    public function obtenerAlumnosPorEvaluacion($id)
    {
        Log::info("🔍 Obteniendo alumnos para evaluación ID: $id");

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:evaluaciones,id',
        ]);

        if ($validator->fails()) {
            Log::error("Validación fallida para evaluación ID: $id", $validator->errors()->toArray());
            return response()->json([
                'message' => 'El ID de la evaluación no es válido o no existe.',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            // QUERY SIMPLIFICADA Y SIN DUPLICADOS
            $evaluacionesAlumnos = DB::table('evaluaciones_alumno as ea')
                ->join('alumnos as a', 'ea.alumno_id', '=', 'a.id')
                ->join('evaluaciones as e', 'ea.evaluacion_id', '=', 'e.id')
                ->select(
                    'a.id',
                    'a.nombres',
                    'a.apellidos',
                    'a.dni',
                    'a.email',
                    'a.celular',
                    'ea.nota',
                    'ea.asistencia',
                    'ea.evaluacion_id',
                    'e.tipo_evaluacion_id'
                )
                ->where('ea.evaluacion_id', $id)
                ->distinct() // AÑADIR DISTINCT para evitar duplicados
                ->orderBy('a.apellidos', 'asc')
                ->orderBy('a.nombres', 'asc')
                ->get();

            Log::info("✅ Alumnos obtenidos exitosamente", [
                'count' => $evaluacionesAlumnos->count(),
                'evaluacion_id' => $id
            ]);

            // Debug: Log de los primeros registros
            if ($evaluacionesAlumnos->count() > 0) {
                Log::info("📊 Muestra de datos:", [
                    'primer_alumno' => $evaluacionesAlumnos->first(),
                    'ids_alumnos' => $evaluacionesAlumnos->pluck('id')->toArray()
                ]);
            }

            return response()->json($evaluacionesAlumnos);
        } catch (\Exception $e) {
            Log::error("❌ Error al obtener alumnos: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
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
