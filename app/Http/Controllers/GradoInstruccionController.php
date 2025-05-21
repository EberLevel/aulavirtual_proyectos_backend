<?php

namespace App\Http\Controllers;

use App\Models\GradoInstruccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradoInstruccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        $gradoInstruccion = GradoInstruccion::where('domain_id', $domain_id)->paginate(10);
    
        return response()->json($gradoInstruccion, 200);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del request
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'nivel' => 'required',
            'porcentaje' => 'required|numeric',
        ]);
    
        // Si la validación falla, retorna un error
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        // Si la validación es exitosa, crear el registro
        $gradoInstrucion = GradoInstruccion::create($request->all());
    
        return response()->json(['message' => 'Grado de instrucción creado correctamente', 'data' => $gradoInstrucion], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $gradoInstrucion = GradoInstruccion::find($id);

        if (!$gradoInstrucion) {
            return response()->json(['message' => 'Grado de instrucción no encontrado'], 404);
        }

        return response()->json(['data' => $gradoInstrucion], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos del request
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'nivel' => 'required',
            'porcentaje' => 'required|numeric',
        ]);
    
        // Si la validación falla, retorna un error
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        // Si la validación es exitosa, buscar el registro por ID
        $gradoInstrucion = GradoInstruccion::find($id);
    
        if (!$gradoInstrucion) {
            return response()->json(['message' => 'Grado de instrucción no encontrado'], 404);
        }
    
        // Actualizar el grado de instrucción
        $gradoInstrucion->update($request->all());
    
        return response()->json(['message' => 'Grado de instrucción actualizado correctamente', 'data' => $gradoInstrucion], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gradoInstrucion = GradoInstruccion::find($id);

        if (!$gradoInstrucion) {
            return response()->json(['message' => 'Grado de instrucción no encontrado'], 404);
        }

        $gradoInstrucion->delete();

        return response()->json(['message' => 'Grado de instrucción eliminado correctamente'], 204);
    }
}
