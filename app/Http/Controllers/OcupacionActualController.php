<?php

namespace App\Http\Controllers;

use App\Models\OcupacionActual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OcupacionActualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        // Filtrar por domain_id
        $ocupacion = OcupacionActual::where('domain_id', $domain_id)->paginate(10);
        return response()->json($ocupacion, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id', 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Creación de Ocupación Actual
        $ocupacion = OcupacionActual::create($request->all());

        return response()->json([
            'message' => 'Ocupación actual creada correctamente',
            'data' => $ocupacion,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ocupacion = OcupacionActual::find($id);

        if (!$ocupacion) {
            return response()->json(['message' => 'Ocupación no encontrada'], 404);
        }

        return response()->json(['data' => $ocupacion], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ocupacion = OcupacionActual::find($id);

        if (!$ocupacion) {
            return response()->json(['message' => 'Ocupación no encontrada'], 404);
        }

        // Actualizar la ocupación
        $ocupacion->update($request->all());

        return response()->json([
            'message' => 'Ocupación actualizada correctamente',
            'data' => $ocupacion,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ocupacion = OcupacionActual::find($id);

        if (!$ocupacion) {
            return response()->json(['message' => 'Ocupación no encontrada'], 404);
        }

        $ocupacion->delete();

        return response()->json(['message' => 'Ocupación eliminada correctamente'], 204);
    }
}
