<?php

namespace App\Http\Controllers;

use App\Models\NivelCargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 

class NivelCargoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        // Obtener los niveles de cargo filtrados por domain_id
        $niveles = NivelCargo::where('domain_id', $domain_id)->paginate(10);

        return response()->json($niveles, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Usar Validator en lugar de validate()
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id',  // Verifica que el dominio exista
        ]);

        // Si la validación falla
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Creación de un nuevo nivel de cargo
        $nivel = NivelCargo::create($request->all());

        return response()->json(['message' => 'Nivel creado correctamente', 'data' => $nivel], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $nivel = NivelCargo::find($id);

        if (!$nivel) {
            return response()->json(['message' => 'Nivel no encontrado'], 404);
        }

        return response()->json(['data' => $nivel], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Usar Validator en lugar de validate()
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id',
        ]);

        // Si la validación falla
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $nivel = NivelCargo::find($id);

        if (!$nivel) {
            return response()->json(['message' => 'Nivel no encontrado'], 404);
        }

        // Actualizar el nivel de cargo
        $nivel->update($request->all());

        return response()->json(['message' => 'Nivel actualizado correctamente', 'data' => $nivel], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $nivel = NivelCargo::find($id);

        if (!$nivel) {
            return response()->json(['message' => 'Nivel no encontrado'], 404);
        }

        $nivel->delete();

        return response()->json(['message' => 'Nivel eliminado correctamente'], 200);
    }
}
