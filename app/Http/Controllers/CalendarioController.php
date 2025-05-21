<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarioController extends Controller
{
    public function getAlumnoCalendario(Request $request)
    {
            $alumnoId = $request->input('alumno_id');
            $domainId = $request->input('domain_id');
            $eventos = DB::table('alumnos as a')
                ->select('a.id', 'c.nombre')
                ->selectRaw('COALESCE(
            (SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    "day_id", ch.day_id,
                    "hora_inicio",ch.hora_inicio,
                    "hora_fin",ch.hora_fin ,
                    "fecha_inicio",ch.fecha_inicio,
                    "fecha_fin",ch.fecha_fin,
                    "docente_name",d.nombres,
                    "aula_name",a.nombre,
                    "aula_ubication",a.descripcion
                )
            )
            FROM curso_horario ch
            join docentes d on d.id =ch.docente_id  
            join aulas a on a.id = ch.aula_id
            WHERE ch.curso_id = ca.curso_id
            and ch.domain_id ='.$domainId.'
            ), "[]") AS horarios')
                ->join('curso_alumno as ca', 'ca.alumno_id', '=', 'a.id')
                ->join('cursos as c', 'c.id', '=', 'ca.curso_id')
                ->where('a.id', $alumnoId)
                ->groupBy('a.id', 'c.nombre', 'horarios')
                ->get();    
        return response()->json($eventos);
    }

    public function getDocenteCalendario(Request $request)
    {
        $docenteId = $request->input('docente_id');
        $domainId = $request->input('domain_id'); // Usar el domain_id del request
    
        $eventos = DB::table('curso_horario as ch')
            ->join('cursos as c', 'c.id', '=', 'ch.curso_id') // Unir con la tabla cursos
            ->select('ch.curso_id', 'c.nombre', 'ch.day_id', 'ch.hora_inicio', 'ch.hora_fin', 'ch.fecha_inicio', 'ch.fecha_fin')
            ->where('ch.docente_id', $docenteId)
            ->where('ch.domain_id', $domainId)
            ->get();
    
        $eventosFormateados = $eventos->groupBy('curso_id')->map(function ($horarios, $cursoId) {
            return [
                'curso_id' => $cursoId,
                'nombre' => $horarios->first()->nombre,
                'horarios' => $horarios->map(function ($horario) {
                    return [
                        'day_id' => $horario->day_id,
                        'hora_inicio' => $horario->hora_inicio,
                        'hora_fin' => $horario->hora_fin,
                        'fecha_inicio' => $horario->fecha_inicio,
                        'fecha_fin' => $horario->fecha_fin,
                    ];
                })->toArray()
            ];
        })->values();
    
        return response()->json($eventosFormateados);
    }
    

    
}
