<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use Illuminate\Http\Request;

class PreguntaController extends Controller
{
    public function index($domain_id, $evaluacion_id)
    {
        $preguntas = Pregunta::where('domain_id', $domain_id)
            ->where('evaluacion_id', $evaluacion_id)
            ->get();
        return response()->json($preguntas);
    }


    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'pregunta_docente' => 'required',
                'evaluacion_id' => 'required',
                'valor_pregunta' => 'required|numeric',
                'tipo_de_evaluacion_id' => 'required',
            ]);
    
            $pregunta = Pregunta::create($request->all());
    
            return response()->json($pregunta, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $pregunta = Pregunta::findOrFail($id);
        return response()->json($pregunta);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pregunta_docente' => 'required',
            'evaluacion_id' => 'required',
            'alternativas' => 'required',
            'respuesta_correcta' => 'required',
            'valor_pregunta' => 'required|numeric',
        ]);

        $pregunta = Pregunta::findOrFail($id);
        $pregunta->update($request->all());

        return response()->json($pregunta);
    }

    public function destroy($id)
    {
        $pregunta = Pregunta::findOrFail($id);
        $pregunta->delete();

        return response()->json(null, 204);
    }
}