<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForoController extends Controller
{
    public function store(Request $request){
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'id_curso' => 'required|integer',
        ]);

        $foro = DB::table('foros')->insert([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_curso' => $request->id_curso,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'domain_id' => $request->domain_id,
        ]);

        return response()->json($foro, 201);

    }
    public function show($domain_id, $alumno_id = null, $docente_id = null)
    {
        $query = DB::table('cursos AS c')
            ->select(
                'c.nombre AS curso_name',
                'd.nombres AS docente_name',

                DB::raw("
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'fecha_inicio', f.fecha_inicio,
                            'fecha_fin', f.fecha_fin,
                            'foro_name', f.nombre,
                            'foro_pregunta', f.descripcion,
                            'id_foro', f.id,
                            'respuestas', IFNULL((
                                SELECT JSON_ARRAYAGG(
                                    JSON_OBJECT(
                                        'id', fr.id,
                                        'alumno', CONCAT(a.nombres, ' ', a.apellidos),
                                        'respuesta', fr.respuesta,
                                        'fecha', fr.created_at,
                                        'subrespuestas', IFNULL((
                                            SELECT JSON_ARRAYAGG(
                                                JSON_OBJECT(
                                                    'id', sr.id,
                                                    'alumno', CONCAT(sa.nombres, ' ', sa.apellidos),
                                                    'respuesta', sr.respuesta,
                                                    'fecha', sr.created_at
                                                )
                                            )
                                            FROM foro_respuestas sr
                                            JOIN alumnos sa ON sa.id = sr.alumno_id
                                            WHERE sr.foro_respuesta_id = fr.id
                                        ), JSON_ARRAY())
                                    )
                                )
                                FROM foro_respuestas AS fr
                                JOIN alumnos AS a ON a.id = fr.alumno_id
                                WHERE fr.foro_id = f.id AND fr.foro_respuesta_id IS NULL
                            ), JSON_ARRAY())
                        )
                    ) AS foros
                ")
            )
            ->join('foros AS f', 'f.id_curso', '=', 'c.id')
            ->join('docentes AS d', 'd.id', '=', 'c.docente_id')
            ->where('c.domain_id', $domain_id);
    
        if ($alumno_id !== null) {
            $query->join('curso_alumno AS m', 'm.curso_id', '=', 'c.id')
                ->where('m.alumno_id', $alumno_id);
        } elseif ($docente_id !== null) {
            $query->where('c.docente_id', $docente_id);
        }
    
        $results = $query->groupBy('c.id', 'c.nombre', 'd.nombres')
            ->get()
            ->map(function ($item) {
                return [
                    'curso_name' => $item->curso_name,
                    'docente_name' => $item->docente_name,
                    'foros' => json_decode($item->foros)
                    
                ];
            });
    
        return response()->json($results, 200);
    }
    public function storeMessage(Request $request){
        $this->validate($request, [
            'id_foro' => 'required|integer',
            'alumno_id' => 'required|integer',
            'respuesta' => 'required|required'
        ]);

        $foro = DB::table('foro_respuestas')->insert([
            'foro_id' => $request->id_foro,
            'alumno_id' => $request->alumno_id,
            'domain_id' => $request->domain_id,
            'foro_respuesta_id' => $request->id_respuesta   ,
            'respuesta' => $request->respuesta,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return response()->json($foro, 201);

    }
    
}