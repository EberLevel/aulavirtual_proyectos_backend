<?php

namespace App\Http\Controllers;

use App\Models\EstadoAvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstadoAvanceController extends Controller
{
    /**
     * Display a listing of the resource filtered by domain_id.
     */
    public function index($domain_id) // Agregar el domain_id como parámetro
    {
        // Obtener los estados de avance filtrados por el domain_id
        $EstadoAvance = EstadoAvance::where('domain_id', $domain_id)->paginate(10);

        return response()->json($EstadoAvance, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id', // Verificar que el domain_id exista
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Crear el estado de avance
        $EstadoAvance = EstadoAvance::create($request->all());

        return response()->json(['message' => 'Estado de avance creado correctamente', 'data' => $EstadoAvance], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Buscar el estado de avance por ID
        $EstadoAvance = EstadoAvance::find($id);

        if (!$EstadoAvance) {
            return response()->json(['message' => 'Estado de avance no encontrado'], 404);
        }

        return response()->json(['data' => $EstadoAvance], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Buscar el estado de avance por ID
        $EstadoAvance = EstadoAvance::find($id);

        if (!$EstadoAvance) {
            return response()->json(['message' => 'Estado de avance no encontrado'], 404);
        }

        // Actualizar el estado de avance
        $EstadoAvance->update($request->all());

        return response()->json(['message' => 'Estado de avance actualizado correctamente', 'data' => $EstadoAvance], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Buscar el estado de avance por ID
        $EstadoAvance = EstadoAvance::find($id);

        if (!$EstadoAvance) {
            return response()->json(['message' => 'Estado de avance no encontrado'], 404);
        }

        // Eliminar el estado de avance
        $EstadoAvance->delete();

        return response()->json(['message' => 'Estado de avance eliminado correctamente'], 204);
    }
}
