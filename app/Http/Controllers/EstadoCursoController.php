<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EstadoCurso;
use App\Models\Carrera;
use Illuminate\Support\Facades\DB;

// TB Plan de Estudio
class EstadoCursoController extends Controller
{
    public function index($domain_id)
    {
        $areas = EstadoCurso::where('domain_id', $domain_id)
            ->whereNull('deleted_at')
            ->get();


        return response()->json($areas);
    }

    public function store(Request $request, $domain_id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'color' => 'string|max:255',
        ]);


        $data = $request->all();
        $data['domain_id'] = $domain_id;

        $area = EstadoCurso::create($data);
        return response()->json($area, 201);
    }

    public function show($id)
    {
        $area = EstadoCurso::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }
        return response()->json($area);
    }

    public function update(Request $request, $domain_id, $id)
    {

        $this->validate($request, [
            'nombre' => 'string|max:255',
            'color' => 'string|max:255',
        ]);


        $area = EstadoCurso::find($id);


        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->update($request->all());
        return response()->json($area);
    }

    public function destroy($domain_id, $id)
    {
        $area = EstadoCurso::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->delete();
        return response()->json(['mensaje' => 'Área eliminada', 'status' => 200], 200);
    }

    public function restore($id)
    {
        $area = EstadoCurso::withTrashed()->find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->restore();
        return response()->json(['mensaje' => 'Área restaurada', 'status' => 200], 200);
    }

    /**
     * Lista el plan de estudios asociado a una carrera específica.
     */
    public function listarPlanPorCarrera($carrera_id)
    {
        // Verificar si la carrera existe
        $carrera = Carrera::find($carrera_id);
    
        if (!$carrera) {
            return response()->json(['mensaje' => 'Carrera no encontrada', 'status' => 404], 404);
        }
    
        // Realizar la consulta para obtener estado_id y nombre del plan de estudio
        $planesDeEstudio = DB::table('cursos')
            ->join('plan_de_estudios', 'cursos.estado_id', '=', 'plan_de_estudios.id')
            ->select('cursos.estado_id', 'plan_de_estudios.nombre as nombre_plan_de_estudio')
            ->where('cursos.carrera_id', $carrera_id)
            ->groupBy('cursos.estado_id', 'plan_de_estudios.nombre')
            ->get();
    
        // Verificar si hay planes de estudio relacionados
        if ($planesDeEstudio->isEmpty()) {
            return response()->json(['mensaje' => 'No se encontraron planes de estudio para esta carrera', 'status' => 404], 404);
        }
    
        // Retornar los planes de estudio encontrados
        return response()->json($planesDeEstudio);
    }
    
}
