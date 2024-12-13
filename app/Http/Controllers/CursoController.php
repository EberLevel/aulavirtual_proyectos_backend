<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Support\Facades\DB;

class CursoController extends Controller
{
    public function index($id)
    {
        $courses = Curso::leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')
            ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
            ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id') // Usar la tabla area_de_formacion
            ->leftJoin('estados', 'estados.id', '=', 'cursos.estado_id') // Usar la tabla estados
            ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
            ->where('cursos.carrera_id', $id)
            ->select(
                'cursos.*',
                'ciclos.nombre as ciclo_nombre',
                'modulos_formativos.nombre as modulo_formativo_nombre',
                'area_de_formacion.nombre as area_de_formacion_nombre', // Usar el campo nombre desde la tabla area_de_formacion
                'carreras.nombres as carrera_nombre',
                'estados.nombre as estado_nombre', // Usar el campo nombre desde la tabla estados
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombre'
            )
            ->get();

        return response()->json($courses);
    }

    public function getCursosByAlumno($alumnoId)
    {
        $courses = Curso::leftJoin('curso_alumno', 'curso_alumno.curso_id', '=', 'cursos.id')
            ->leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')
            ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
            ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id') // Usar la tabla area_de_formacion
            ->leftJoin('estados', 'estados.id', '=', 'cursos.estado_id') // Usar la tabla estados
            ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
            ->where('curso_alumno.alumno_id', $alumnoId)
            ->select(
                'cursos.*',
                'ciclos.nombre as ciclo_nombre',
                'modulos_formativos.nombre as modulo_formativo_nombre',
                'area_de_formacion.nombre as area_de_formacion_nombre', // Usar el campo nombre desde la tabla area_de_formacion
                'carreras.nombres as carrera_nombre',
                'estados.nombre as estado_nombre', // Usar el campo nombre desde la tabla estados
                'docentes.id as docente_id'
            )
            ->get();

        return response()->json($courses);
    }


    public function getCursosByDomain($domainId)
    {
        $courses = Curso::leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')
            ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
            ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id') // Usar la tabla area_de_formacion
            ->leftJoin('estados', 'estados.id', '=', 'cursos.estado_id') // Usar la tabla estados
            ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
            ->where('cursos.domain_id', $domainId)
            ->select(
                'cursos.*',
                'ciclos.nombre as ciclo_nombre',
                'modulos_formativos.nombre as modulo_formativo_nombre',
                'area_de_formacion.nombre as area_de_formacion_nombre', // Usar el campo nombre desde la tabla area_de_formacion
                'carreras.nombres as carrera_nombre',
                'estados.nombre as estado_nombre', // Usar el campo nombre desde la tabla estados
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombre'
            )
            ->get();

        return response()->json($courses);
    }

    public function getCursosByPlanEstudioYCarrera($planEstudioId, $carreraId,$alumnoId=null)
    {
        if($alumnoId){
            //select all curso_alumno where alumno_id = $alumnoId
            return DB::table('curso_alumno')
                ->leftJoin('cursos', 'curso_alumno.curso_id', '=', 'cursos.id')
                ->leftJoin('carreras', 'cursos.carrera_id', '=', 'carreras.id')
                ->leftJoin('plan_de_estudios', 'cursos.estado_id', '=', 'plan_de_estudios.id')
                ->leftJoin('ciclos', 'cursos.ciclo_id', '=', 'ciclos.id')
                ->leftJoin('area_de_formacion', 'cursos.area_de_formacion_id', '=', 'area_de_formacion.id')
                ->leftJoin('modulos_formativos', 'cursos.modulo_formativo_id', '=', 'modulos_formativos.id')
                ->select(
                    'cursos.*',
                    'carreras.nombres as carrera_nombre',
                    'plan_de_estudios.nombre as plan_de_estudio_nombre',
                    'ciclos.nombre as ciclo_nombre', // Nombre del ciclo
                    'area_de_formacion.nombre as area_formacion_nombre', // Nombre del área de formación
                    'modulos_formativos.nombre as modulo_formativo_nombre' // Nombre del módulo formativo
                )
                ->where('curso_alumno.alumno_id', $alumnoId)
                ->where('cursos.estado_id', $planEstudioId)
                ->where('cursos.carrera_id', $carreraId)
                ->where('curso_alumno.estado_id', 2)
                ->get();
        }

        return DB::table('cursos')
            ->join('carreras', 'cursos.carrera_id', '=', 'carreras.id')
            ->join('plan_de_estudios', 'cursos.estado_id', '=', 'plan_de_estudios.id')
            ->leftJoin('ciclos', 'cursos.ciclo_id', '=', 'ciclos.id')
            ->leftJoin('area_de_formacion', 'cursos.area_de_formacion_id', '=', 'area_de_formacion.id')
            ->leftJoin('modulos_formativos', 'cursos.modulo_formativo_id', '=', 'modulos_formativos.id')
            ->select(
                'cursos.*',
                'carreras.nombres as carrera_nombre',
                'plan_de_estudios.nombre as plan_de_estudio_nombre',
                'ciclos.nombre as ciclo_nombre', // Nombre del ciclo
                'area_de_formacion.nombre as area_formacion_nombre', // Nombre del área de formación
                'modulos_formativos.nombre as modulo_formativo_nombre' // Nombre del módulo formativo
            )
            ->where('cursos.estado_id', $planEstudioId)
            ->where('cursos.carrera_id', $carreraId)
            ->get();
    }
    
    



    public function store(Request $request)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombreCurso' => 'required|string|max:255',
            'cicloId' => 'required|integer',
            'areaFormacionId' => 'required|integer',
            'moduloFormativoId' => 'required|integer',
            'cantidadCreditos' => 'required|integer',
            'porcentajeCreditos' => 'required|integer',
            'cantidadHoras' => 'required|integer',
            'horasPracticas' => 'required|integer',
            'carreraId' => 'required|integer',
            'syllabus' => 'required|string',
            'tema' => 'required|string',
            'estadoId' => 'required|integer',
            'domain_id' => 'required',
            'asignacionDocentesId' => 'required',
        ]);

        $curso = Curso::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombreCurso,
            'ciclo_id' => $request->cicloId,
            'area_de_formacion_id' => $request->areaFormacionId,
            'modulo_formativo_id' => $request->moduloFormativoId,
            'cantidad_de_creditos' => $request->cantidadCreditos,
            'porcentaje_de_creditos' => $request->porcentajeCreditos,
            'cantidad_de_horas' => $request->cantidadHoras,
            'horas_practicas' => $request->horasPracticas,
            'carrera_id' => $request->carreraId,
            'syllabus' => $request->syllabus,
            'tema' => $request->tema,
            'estado_id' => $request->estadoId,
            'domain_id' => $request->domain_id,
            'docente_id' => is_array($request->asignacionDocentesId) ? null : $request->asignacionDocentesId,
        ]);

        return response()->json($curso, 201);
    }


    public function show($id)
    {
        $course = Curso::find($id);
        if (!$course) {
            return response()->json(['Error' => 'Curso no encontrado'], 404);
        }

        return response()->json(['Exito' => true, 'Datos' => $course], 200);
    }

    public function getSyllabus($id)
    {
        $course = Curso::find($id, ['id', 'syllabus']);
        if (!$course) {
            return response()->json(['Error' => 'Curso no encontrado'], 404);
        }

        return response()->json(['Exito' => true, 'Datos' => $course], 200);
    }

    public function getTema($id)
    {
        $course = Curso::find($id, ['id', 'tema']);
        if (!$course) {
            return response()->json(['Error' => 'Curso no encontrado'], 404);
        }

        return response()->json(['Exito' => true, 'Datos' => $course], 200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombreCurso' => 'required|string|max:255',
            'cicloId' => 'required|integer',
            'areaFormacionId' => 'required|integer',
            'moduloFormativoId' => 'required|integer',
            'cantidadCreditos' => 'required|integer',
            'porcentajeCreditos' => 'required|integer',
            'cantidadHoras' => 'required|integer',
            'horasPracticas' => 'required|integer',
            'carreraId' => 'required|integer',
            'syllabus' => 'required|string',
            'tema' => 'required|string',
            'estadoId' => 'required|integer',
            'domain_id' => 'required',
            'asignacionDocentesId' => 'required',
        ]);

        $curso = Curso::findOrFail($id);
        $curso->update([
            'codigo' => $request->codigo,
            'nombre' => $request->nombreCurso,
            'ciclo_id' => $request->cicloId,
            'area_de_formacion_id' => $request->areaFormacionId,
            'modulo_formativo_id' => $request->moduloFormativoId,
            'cantidad_de_creditos' => $request->cantidadCreditos,
            'porcentaje_de_creditos' => $request->porcentajeCreditos,
            'cantidad_de_horas' => $request->cantidadHoras,
            'horas_practicas' => $request->horasPracticas,
            'carrera_id' => $request->carreraId,
            'syllabus' => $request->syllabus,
            'tema' => $request->tema,
            'estado_id' => $request->estadoId,
            'domain_id' => $request->domain_id,
            'docente_id' => is_array($request->asignacionDocentesId) ? null : $request->asignacionDocentesId,
        ]);

        return response()->json($curso, 200);
    }

    public function getAllCursos()
    {
        $courses = Curso::leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')
            ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
            ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')
            ->leftJoin('estados', 'estados.id', '=', 'cursos.estado_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
            ->select(
                'cursos.*',
                'ciclos.nombre as ciclo_nombre',
                'modulos_formativos.nombre as modulo_formativo_nombre',
                'area_de_formacion.nombre as area_de_formacion_nombre',
                'carreras.nombres as carrera_nombre',
                'estados.nombre as estado_nombre',
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombre'
            )
            ->get();

        return response()->json($courses);
    }




    public function destroy($id)
    {
        // Busca el curso por su ID
        $curso = Curso::find($id);

        // Verifica si el curso existe
        if (!$curso) {
            return response()->json([
                'message' => 'Curso no encontrado'
            ], 404);
        }

        // Elimina el curso
        $curso->delete();

        // Devuelve una respuesta de éxito
        return response()->json([
            'message' => 'Curso eliminado exitosamente'
        ], 200);
    }
}
