<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            ->leftJoin('plan_de_estudios', 'plan_de_estudios.id', '=', 'cursos.estado_id')
            ->where('cursos.domain_id', $domainId)
            ->select(
                'cursos.*',
                'ciclos.nombre as ciclo_nombre',
                'modulos_formativos.nombre as modulo_formativo_nombre',
                'area_de_formacion.nombre as area_de_formacion_nombre', // Usar el campo nombre desde la tabla area_de_formacion
                'carreras.nombres as carrera_nombre',
                'estados.nombre as estado_nombre', // Usar el campo nombre desde la tabla estados
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombre',
                'plan_de_estudios.nombre as plan_estudios_nombre'

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
            'asignacionDocentesId' => 'nullable',
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

    public function storeMasivos(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar que se reciba un array de cursos directamente
            $this->validate($request, [
                '*.codigo' => 'required|string|max:255',
                '*.nombreCurso' => 'required|string|max:255',
                '*.cicloId' => 'required|string',
                '*.areaFormacionId' => 'required|string',
                '*.moduloFormativoId' => 'required|string',
                '*.cantidadCreditos' => 'required|integer',
                '*.porcentajeCreditos' => 'required|integer',
                '*.cantidadHoras' => 'required|integer',
                '*.horasPracticas' => 'required|integer',
                '*.carreraId' => 'required|string',
                '*.estadoId' => 'required|string',
                '*.domain_id' => 'required',
                '*.asignacionDocentesId' => 'nullable',
            ]);

            // Validar que el request sea un array
            $cursosData = $request->all();
            if (!is_array($cursosData) || empty($cursosData)) {
                return response()->json(['error' => 'Se requiere un array de cursos'], 400);
            }

            $cursosCreados = [];
            $cursosActualizados = [];

            // Procesar cada curso del array
            foreach ($cursosData as $index => $cursoData) {

                // Verificar si el curso ya existe por código
                $cursoExistente = Curso::where('codigo', $cursoData['codigo'])
                    ->where('domain_id', $cursoData['domain_id'])
                    ->first();

                // Validar que el ciclo existe
                $cicloExist = DB::table('ciclos')->where('nombre', $cursoData['cicloId'])
                    ->where('domain_id', $cursoData['domain_id'])->first();
                if (!$cicloExist) {
                    throw new \Exception("Curso en posición {$index}: El ciclo con ID {$cursoData['cicloId']} no existe");
                }
                $cursoData['cicloId'] = $cicloExist->id;

                // Validar que la carrera existe
                $carreraExist = DB::table('carreras')->where('nombres', $cursoData['carreraId'])
                    ->where('domain_id', $cursoData['domain_id'])->first();
                if (!$carreraExist) {
                    throw new \Exception("Curso en posición {$index}: La carrera con ID {$cursoData['carreraId']} no existe");
                }
                $cursoData['carreraId'] = $carreraExist->id;

                // Validar que el área de formación existe
                $areaExist = DB::table('area_de_formacion')->where('nombre', $cursoData['areaFormacionId'])
                    ->where('domain_id', $cursoData['domain_id'])->first();
                if (!$areaExist) {
                    throw new \Exception("Curso en posición {$index}: El área de formación con ID {$cursoData['areaFormacionId']} no existe");
                }
                $cursoData['areaFormacionId'] = $areaExist->id;

                // Validar que el módulo formativo existe
                $moduloExist = DB::table('modulos_formativos')->where('nombre', $cursoData['moduloFormativoId'])
                    ->where('domain_id', $cursoData['domain_id'])->first();
                if (!$moduloExist) {
                    throw new \Exception("Curso en posición {$index}: El módulo formativo con ID {$cursoData['moduloFormativoId']} no existe");
                }
                $cursoData['moduloFormativoId'] = $moduloExist->id;

                // Validar que el estado existe
                $estadoExist = DB::table('plan_de_estudios')->where('nombre', $cursoData['estadoId'])
                    ->where('domain_id', $cursoData['domain_id'])->first();
                if (!$estadoExist) {
                    throw new \Exception("Curso en posición {$index}: El estado con ID {$cursoData['estadoId']} no existe");
                }
                $cursoData['estadoId'] = $estadoExist->id;

                // Validar docente si se proporciona
                if (!empty($cursoData['asignacionDocentesId']) && !is_array($cursoData['asignacionDocentesId'])) {
                    $docenteExist = DB::table('docentes')->where('id', $cursoData['asignacionDocentesId'])->first();
                    if (!$docenteExist) {
                        throw new \Exception("Curso en posición {$index}: El docente con ID {$cursoData['asignacionDocentesId']} no existe");
                    }
                }

                // Preparar datos del curso
                $cursoDataForDB = [
                    'codigo' => $cursoData['codigo'],
                    'nombre' => $cursoData['nombreCurso'],
                    'ciclo_id' => $cursoData['cicloId'],
                    'area_de_formacion_id' => $cursoData['areaFormacionId'],
                    'modulo_formativo_id' => $cursoData['moduloFormativoId'],
                    'cantidad_de_creditos' => $cursoData['cantidadCreditos'],
                    'porcentaje_de_creditos' => $cursoData['porcentajeCreditos'],
                    'cantidad_de_horas' => $cursoData['cantidadHoras'],
                    'horas_practicas' => $cursoData['horasPracticas'],
                    'carrera_id' => $cursoData['carreraId'],
                    'syllabus' => $cursoData['syllabus'] ?? null,
                    'tema' => $cursoData['tema'] ?? null,
                    'estado_id' => $cursoData['estadoId'],
                    'domain_id' => $cursoData['domain_id'],
                    'docente_id' => is_array($cursoData['asignacionDocentesId']) ? null : $cursoData['asignacionDocentesId'],
                    'updated_at' => Carbon::now()
                ];

                $curso = null;
                $esActualizacion = false;

                if ($cursoExistente) {
                    // ACTUALIZAR curso existente
                    $cursoExistente->update($cursoDataForDB);
                    $curso = $cursoExistente->fresh(); // Obtener datos actualizados
                    $esActualizacion = true;

                } else {
                    // CREAR nuevo curso
                    $cursoDataForDB['created_at'] = Carbon::now();
                    $curso = Curso::create($cursoDataForDB);
                }

                // Agregar a la lista correspondiente
                $cursoInfo = [
                    'curso_id' => $curso->id,
                    'codigo' => $curso->codigo,
                    'nombre' => $curso->nombre,
                    'carrera_id' => $curso->carrera_id,
                    'ciclo_id' => $curso->ciclo_id
                ];

                if ($esActualizacion) {
                    $cursosActualizados[] = $cursoInfo;
                } else {
                    $cursosCreados[] = $cursoInfo;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proceso completado: ' . count($cursosCreados) . ' cursos creados, ' . count($cursosActualizados) . ' cursos actualizados',
                'cursos_creados' => count($cursosCreados),
                'cursos_actualizados' => count($cursosActualizados),
                'total_procesados' => count($cursosData),
                'cursos_nuevos' => $cursosCreados,
                'cursos_actualizados' => $cursosActualizados
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error para debugging
            \Log::error('Error al crear/actualizar cursos masivos: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al procesar los cursos: ' . $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
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
            'porcentajeCreditos' => 'required',
            'cantidadHoras' => 'required|integer',
            'horasPracticas' => 'required|integer',
            'carreraId' => 'required|integer',
            'syllabus' => 'required|string',
            'tema' => 'required|string',
            'estadoId' => 'required|integer',
            'domain_id' => 'required',
            'asignacionDocentesId' => 'nullable',
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

    public function getAllCursos($domain_id)
    {
        $courses = Curso::leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')
            ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')
            ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')
            ->leftJoin('estados', 'estados.id', '=', 'cursos.estado_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
            ->leftJoin('plan_de_estudios', 'plan_de_estudios.id', '=', 'cursos.estado_id')
            ->select(
                'cursos.*',
                'ciclos.nombre as ciclo_nombre',
                'modulos_formativos.nombre as modulo_formativo_nombre',
                'area_de_formacion.nombre as area_de_formacion_nombre',
                'carreras.nombres as carrera_nombre',
                'estados.nombre as estado_nombre',
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombre',
                'plan_de_estudios.nombre as plan_estudios_nombre'
            )
            ->where('cursos.domain_id', $domain_id)
            ->get();

        return response()->json($courses);
    }

    public function getTotalCreditsByCurso($domain_id)
    {
        $totalCreditos = Curso::where('domain_id', $domain_id)->sum('cantidad_de_creditos');
        return response()->json(['total_creditos' => $totalCreditos], 200);
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
