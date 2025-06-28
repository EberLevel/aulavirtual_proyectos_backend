<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Docente;

class CursoDocenteController extends Controller
{
    // Método para listar cursos por docente
    public function index($docente_id)
    {
        try {
            $courses = Curso::leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')  // Reemplaza t_g_parametros con ciclos
                ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')  // Reemplaza t_g_parametros con modulos_formativos
                ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')  // Reemplaza t_g_parametros con area_de_formacion
                ->leftJoin('estados', 'estados.id', '=', 'cursos.estado_id')  // Reemplaza t_g_parametros con estados
                ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
                ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
                ->leftJoin('plan_de_estudios', 'plan_de_estudios.id', '=', 'cursos.estado_id')  // Reemplaza t_g_parametros con plan_de_estudios
                ->where('cursos.docente_id', $docente_id)
                ->select(
                    'cursos.*',
                    'ciclos.nombre as ciclo_nombre',  // Obtiene el nombre del ciclo
                    'modulos_formativos.nombre as modulo_formativo_nombre',  // Obtiene el nombre del módulo formativo
                    'area_de_formacion.nombre as area_de_formacion_nombre',  // Obtiene el nombre del área de formación
                    'carreras.nombres as carrera_nombre',
                    'estados.nombre as estado_nombre',  // Obtiene el nombre del estado
                    'docentes.id as docente_id',
                    'plan_de_estudios.nombre as plan_estudios_nombre'
                )
                ->get();

            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Método para asignar un curso a un docente
    public function assign(Request $request)
    {
        try {
            $this->validate($request, [
                'curso_id' => 'required|integer|exists:cursos,id',
                'docente_id' => 'required|integer|exists:docentes,id',
            ]);

            $curso = Curso::findOrFail($request->curso_id);
            $curso->docente_id = $request->docente_id;
            $curso->save();

            return response()->json($curso, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
