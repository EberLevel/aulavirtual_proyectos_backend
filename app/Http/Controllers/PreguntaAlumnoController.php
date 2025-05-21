<?php

namespace App\Http\Controllers;

use App\Models\PreguntaAlumno;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PreguntaAlumnoController extends Controller
{
    public function obtenerPreguntaAlumno($preguntaAlumnoId)
    {
        try {
            // Cargar el registro y las relaciones
            $result = PreguntaAlumno::with('alumno', 'pregunta')
                ->findOrFail($preguntaAlumnoId);  // Usar findOrFail para forzar la excepción si no se encuentra

            if ($result->alumno && $result->pregunta) {
                return response()->json([
                    'nombre_alumno' => $result->alumno->nombres,
                    'apellido_alumno' => $result->alumno->apellidos,
                    'pregunta' => $result->pregunta->pregunta_docente,
                    'respuesta_alumno' => $result->respuesta,
                ], 200);
            } else {
                // Retorna un mensaje si alguna relación no existe
                return response()->json([
                    'message' => 'Alumno o pregunta no encontrados',
                    'debug' => $result,
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function obtenerAlumnosPorPreguntaId($preguntaId)
    {
        try {
            $resultados = DB::table('pregunta_alumno')
                ->join('alumnos', 'pregunta_alumno.alumno_id', '=', 'alumnos.id')
                ->join('preguntas', 'pregunta_alumno.pregunta_id', '=', 'preguntas.id')
                ->where('pregunta_alumno.pregunta_id', $preguntaId)
                ->select(
                    'alumnos.id as alumno_id',
                    'alumnos.nombres as nombre_alumno',
                    'alumnos.apellidos as apellido_alumno',
                    'preguntas.pregunta_docente as pregunta',
                    'pregunta_alumno.respuesta as respuesta_alumno'
                )
                ->get();

            if ($resultados->isEmpty()) {
                return response()->json(['message' => 'No se encontraron alumnos para esta pregunta.'], 404);
            }

            return response()->json($resultados, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function actualizarEstado(Request $request)
    {
        try {
            // Obtener los IDs desde la solicitud
            $pregunta_id = $request->input('pregunta_id');
            $alumno_id = $request->input('alumno_id');

            // Verificar si ambos parámetros están presentes
            if (!$pregunta_id || !$alumno_id) {
                return response()->json(['error' => 'Faltan parámetros para la actualización.'], 400);
            }

            // Buscar la respuesta del alumno a la pregunta
            $preguntaAlumno = PreguntaAlumno::where('pregunta_id', $pregunta_id)
                ->where('alumno_id', $alumno_id)
                ->firstOrFail();

            // Actualizar el estado
            $preguntaAlumno->estado_id = $request->input('estado_id');
            $preguntaAlumno->save();

            return response()->json(['message' => 'Estado actualizado correctamente.'], 200);
        } catch (\Exception $e) {
            // Retorna el mensaje exacto de la excepción
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    public function guardarAlumnoPregunta(Request $request)
    {
        try {
            // Validar la solicitud
            $this->validate($request, [
                'alumno_id' => 'required|integer|exists:alumnos,id',
                'pregunta_id' => 'required|integer|exists:preguntas,id',
            ]);

            $estadoId = isset($request->estado_id) ? $request->estado_id : 0;
            // Buscar si ya existe una relación previa en la tabla pregunta_alumno
            $preguntaAlumno = DB::table('pregunta_alumno')
                ->where('alumno_id', $request->alumno_id)
                ->where('pregunta_id', $request->pregunta_id)
                ->first();

            if ($preguntaAlumno) {
                // Si ya existe, actualizamos la respuesta en lugar de insertar una nueva
                DB::table('pregunta_alumno')
                    ->where('id', $preguntaAlumno->id)
                    ->update([
                        'respuesta' => $request->respuesta,
                        'evaluacion_id' => $request->evaluacion_id,
                        'domain_id' => $request->domain_id,
                        'estado_id' =>  $estadoId,
                        'updated_at' => Carbon::now(),
                    ]);

                return response()->json(['message' => 'Respuesta actualizada correctamente'], 200);
            } else {
                // Si no existe, insertamos una nueva fila
                DB::table('pregunta_alumno')->insert([
                    'alumno_id' => $request->alumno_id,
                    'pregunta_id' => $request->pregunta_id,
                    'respuesta' => $request->respuesta,
                    'evaluacion_id' => $request->evaluacion_id,
                    'domain_id' => $request->domain_id,
                    'estado_id' =>  $estadoId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                return response()->json(['message' => 'Respuesta guardada exitosamente'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function obtenerCursosConEvaluaciones($curso_id)
    {
        try {
            $result = DB::table('cursos as c')
                ->join('grupo_de_evaluaciones as gde', 'c.id', '=', 'gde.curso_id')
                ->join('evaluaciones as e', 'e.grupo_de_evaluaciones_id', '=', 'gde.id')
                ->where('c.id', $curso_id)
                ->select('c.*', 'gde.*', 'e.*')
                ->get();
    
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function obtenerSumaCalificaciones(Request $request)
    {
        try {
            // Validar los parámetros requeridos
            $this->validate($request, [
                'alumno_id' => 'required|integer|exists:alumnos,id',
                'evaluacion_id' => 'required|integer|exists:evaluaciones,id',
            ]);
    
            // Obtener la suma de las calificaciones
            $sumaCalificaciones = DB::table('pregunta_alumno')
                ->where('alumno_id', $request->alumno_id)
                ->where('evaluacion_id', $request->evaluacion_id)
                ->sum('calificacion');
    
            // Retornar la respuesta con la suma
            return response()->json([
                'suma_calificaciones' => $sumaCalificaciones
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function obtenerPreguntasNoCorregidas($pregunta_id)
    {
        try {
            $result = PreguntaAlumno::with('pregunta')
                ->where('pregunta_id', $pregunta_id)
                ->whereNull('calificacion')
                ->get();

            return response()->json($result);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
