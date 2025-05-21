<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AsistenciaCursoController extends Controller
{
    public function show(Request $request)
    {
        try {
            $cursoId = $request->input('curso_id');
            $domainId = $request->input('domain_id');
            $fecha = $request->input('fecha');
    
            $dayOfWeek = date('N', strtotime($fecha));
    
            $horarios = DB::table('curso_horario')
                ->where('curso_id', $cursoId)
                ->where('domain_id', $domainId)
                ->whereDate('fecha_inicio', '<=', $fecha)
                ->whereDate('fecha_fin', '>=', $fecha)
                ->where('day_id', $dayOfWeek)
                ->get();
    
            // Verificar si se encontraron horarios
            if ($horarios->isEmpty()) {
                return response()->json(['error' => 'No se encontraron horarios para la fecha dada.'], 404);
            }
    
            // Obtener los participantes del curso para la fecha proporcionada
            $participantes = DB::table('curso_alumno as ca')
                ->select(
                    'ca.curso_id',
                    'ca.alumno_id',
                    'ca.domain_id',
                    'a.codigo',
                    DB::raw("concat(a.nombres,' ',a.apellidos) as nombres"),
                    DB::raw("(CASE WHEN EXISTS (SELECT 1 FROM curso_asistencia cas2 WHERE cas2.alumno_id = ca.alumno_id AND cas2.fecha = ?) THEN 1 ELSE 0 END) as is_marked", [$fecha])
                )
                ->join('alumnos as a', 'ca.alumno_id', '=', 'a.id')
                ->where('ca.curso_id', $cursoId)
                ->where('ca.domain_id', $domainId)
                ->addBinding([$fecha], 'select') // Añadir el parámetro de fecha al binding
                ->get();
    
            // Retornar los datos obtenidos
            return response()->json(['participantes' => $participantes, 'horarios' => $horarios]);
    
        } catch (\Exception $e) {
            // Capturar cualquier excepción y retornar un error 500 con el mensaje de error detallado
            return response()->json(['error' => 'Error en el servidor: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
    public function getFechasCursoHorario(Request $request)
    {
        try {
            $cursoId = $request->input('curso_id');
            $docenteId = $request->input('docente_id');
            $domainId = $request->input('domain_id');
    
            // Consulta para obtener las fechas de curso_horario
            $fechas = DB::table('curso_horario')
                ->select('fecha_inicio', 'fecha_fin')
                ->where('curso_id', $cursoId)
                ->where('docente_id', $docenteId)
                ->where('domain_id', $domainId)
                ->get();
    
            // Verificar si se encontraron fechas
            if ($fechas->isEmpty()) {
                return response()->json(['error' => 'No se encontraron fechas para los filtros dados.'], 404);
            }
    
            // Retornar las fechas obtenidas
            return response()->json($fechas);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en el servidor: ' . $e->getMessage()], 500);
        }
    }
    
    public function store(Request $request){

        $alumnoId = $request->input('alumno_id');
        $cursoId = $request->input('curso_id');
        $domainId = $request->input('domain_id');
        $fecha = $request->input('fecha');
        $asistencia = DB::table('curso_asistencia')
            ->where('alumno_id',$alumnoId)
            ->where('curso_id',$cursoId)
            ->where('domain_id',$domainId)
            ->where('fecha',$fecha)
            ->first();
        if($asistencia){
            //delete
            DB::table('curso_asistencia')->where('id', $asistencia->id)->delete();
        }else{
            //insert
            DB::table('curso_asistencia')->insert([
                'alumno_id' => $alumnoId,
                'curso_id' => $cursoId,
                'domain_id' => $domainId,
                'fecha' => $fecha
            ]);
        }
        return response()->json('ok');
    }
}
