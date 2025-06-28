<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Alumno;
use App\Models\CursoAlumno;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CursoAlumnoController extends Controller
{

    public function index($alumno_id)
    {
        try {
            // $courses = Curso::leftJoin('curso_alumno', 'curso_alumno.curso_id', '=', 'cursos.id')
            //     ->leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')  // Join con la tabla ciclos
            //     ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')  // Join con la tabla area_de_formacion
            //     ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')  // Join con la tabla modulos_formativos
            //     ->leftJoin('estados', 'estados.id', '=', 'cursos.estado_id')  // Join con la tabla estados
            //     ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            //     ->leftJoin('alumnos', 'alumnos.id', '=', 'curso_alumno.alumno_id')
            //     // Agregar filtro para estadoAlumno
            //     ->where('curso_alumno.alumno_id', $alumno_id)
            //     ->where('alumnos.estadoAlumno', '!=', 'RETIRADO')
            //     ->select(
            //         'cursos.*',
            //         'ciclos.nombre as ciclo_nombre',  // Obtiene el nombre del ciclo
            //         'area_de_formacion.nombre as area_de_formacion_nombre',  // Obtiene el nombre del área de formación
            //         'modulos_formativos.nombre as modulo_formativo_nombre',  // Obtiene el nombre del módulo formativo
            //         'carreras.nombres as carrera_nombre',
            //         'estados.nombre as estado_nombre',  // Obtiene el nombre del estado
            //         'alumnos.id as alumno_id',
            //         'curso_alumno.estado_id as estado_id'
            //     )
            //     ->get();

            $courses = Curso::leftJoin('curso_alumno', 'curso_alumno.curso_id', '=', 'cursos.id')
                ->leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')
                ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')
                ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
                ->leftJoin('estados', 'estados.id', '=', 'cursos.estado_id')
                ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
                ->leftJoin('alumnos', 'alumnos.id', '=', 'curso_alumno.alumno_id')
                ->leftJoin('estado_curso_alumno', 'curso_alumno.estado_curso_id', '=', 'estado_curso_alumno.id') // Nuevo join
                ->where('curso_alumno.alumno_id', $alumno_id)
                ->where('alumnos.estadoAlumno', '!=', 'RETIRADO')
                ->select(
                    'cursos.*',
                    'ciclos.nombre as ciclo_nombre',
                    'area_de_formacion.nombre as area_de_formacion_nombre',
                    'modulos_formativos.nombre as modulo_formativo_nombre',
                    'carreras.nombres as carrera_nombre',
                    'estados.nombre as estado_nombre',
                    'alumnos.id as alumno_id',
                    'curso_alumno.estado_id as estado_id',
                    'estado_curso_alumno.color',
                    DB::raw("IF(estado_curso_alumno.nombre IS NULL, 'PENDIENTE', estado_curso_alumno.nombre) as estadoCursoAlumno")
                )
                ->get();


            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function indexByPlan($alumnoId)
    {
        //     $cursos = DB::table('cursos')
        //     ->join('alumnos', 'alumnos.estado_id', '=', 'cursos.estado_id')
        // ->join('curso_alumno', 'curso_alumno.curso_id', '=', 'cursos.id')
        // ->join('estado_curso_alumno', 'curso_alumno.estado_curso_id', '=', 'estado_curso_alumno.id')
        // ->join('plan_de_estudios', 'plan_de_estudios.id', '=', 'cursos.estado_id')
        // ->join('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')
        // ->join('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
        // ->join('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')
        // ->join('carreras', 'carreras.id', '=', 'cursos.carrera_id')
        // ->join('estados', 'estados.id', '=', 'curso_alumno.estado_id')
        // ->where('curso_alumno.alumno_id', $alumnoId)
        // ->where('alumnos.id', $alumnoId)
        // ->where('alumnos.estadoAlumno', '!=', 'RETIRADO')
        // ->select(
        //     'cursos.*',
        //     'ciclos.nombre as ciclo_nombre',  // Obtiene el nombre del ciclo
        //     'area_de_formacion.nombre as area_de_formacion_nombre',  // Obtiene el nombre del área de formación
        //     'modulos_formativos.nombre as modulo_formativo_nombre',  // Obtiene el nombre del módulo formativo
        //     'carreras.nombres as carrera_nombre',
        //     'estados.nombre as estado_nombre',  // Obtiene el nombre del estado
        //     'alumnos.id as alumno_id',
        //     'curso_alumno.estado_id as estado_id',
        //     'estado_curso_alumno.id',
        //     'estado_curso_alumno.nombre as estadoCursoAlumno'
        // )    ->get();

        $cursos = DB::table('cursos')
            ->join('alumnos', 'alumnos.estado_id', '=', 'cursos.estado_id')
            ->join('curso_alumno', 'curso_alumno.curso_id', '=', 'cursos.id')
            ->leftJoin('estado_curso_alumno', 'curso_alumno.estado_curso_id', '=', 'estado_curso_alumno.id')
            ->join('plan_de_estudios', 'plan_de_estudios.id', '=', 'cursos.estado_id')
            ->join('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')
            ->join('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
            ->join('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')
            ->join('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->where('curso_alumno.alumno_id', $alumnoId)
            ->where('alumnos.id', $alumnoId)
            ->where('alumnos.estadoAlumno', '!=', 'RETIRADO')
            ->select(
                'cursos.*',
                'ciclos.nombre as ciclo_nombre',
                'area_de_formacion.nombre as area_de_formacion_nombre',
                'modulos_formativos.nombre as modulo_formativo_nombre',
                'carreras.nombres as carrera_nombre',
                'alumnos.id as alumno_id',
                'curso_alumno.estado_id as estado_id',
                'estado_curso_alumno.id',
                'estado_curso_alumno.color',
                'plan_de_estudios.nombre as plan_estudio_nombre',
                DB::raw("IF(estado_curso_alumno.nombre IS NULL, 'PENDIENTE', estado_curso_alumno.nombre) as estadoCursoAlumno")
            )
            ->get();



        return $cursos;
    }
    public function indexByAlumno($alumno_id)
    {
        try {
            $courses = Curso::leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
                ->leftJoin('alumnos', 'alumnos.carrera_id', '=', 'carreras.id') // Relación directa con la carrera del alumno
                ->leftJoin('plan_de_estudios', 'plan_de_estudios.id', '=', 'cursos.estado_id') // Relación con el plan de estudios
                ->leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id') // Relación con ciclos
                ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
                ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')
                ->where('alumnos.id', $alumno_id)
                ->where('alumnos.estadoAlumno', '!=', 'RETIRADO')
                ->select(
                    'cursos.*',
                    'carreras.nombres as carrera_nombre', // Nombre de la carrera
                    'plan_de_estudios.nombre as plan_estudio_nombre', // Nombre del plan de estudio
                    'ciclos.nombre as ciclo_nombre', // Nombre del ciclo
                    'modulos_formativos.nombre as modulo_formativo_nombre', // Nombre del módulo (Competencia)
                    'modulos_formativos.nombre as competencia_nombre', // Alias para Competencia
                    'area_de_formacion.nombre as area_de_formacion_nombre', // Nombre del área de formación
                    'alumnos.id as alumno_id' // ID del alumno
                )
                ->get();

            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateCursoEstado(Request $request)
    {
        $rules = [
            'cursoId' => 'required|integer',
            'estadoId' => 'required|integer',
            'alumnoId' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Encuentra el curso alumno que corresponde
        $cursoAlumno = CursoAlumno::where('curso_id', $validatedData['cursoId'])
            ->where('alumno_id', $validatedData['alumnoId'])
            ->first();

        if ($cursoAlumno) {
            $cursoAlumno->estado_id = $validatedData['estadoId'];
            $cursoAlumno->save();

            return response()->json(['message' => 'Estado actualizado con éxito.'], 200);
        } else {
            return response()->json(['message' => 'Curso o alumno no encontrados.'], 404);
        }
    }






    // Método para asignar un curso a un alumno
    public function assign(Request $request)
    {
        try {
            $this->validate($request, [
                'curso_id' => 'required|integer|exists:cursos,id',
                'alumno_id' => 'required|integer|exists:alumnos,id',
            ]);

            $curso = Curso::findOrFail($request->curso_id);
            $curso->alumno_id = $request->alumno_id;
            $curso->save();

            return response()->json($curso, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
