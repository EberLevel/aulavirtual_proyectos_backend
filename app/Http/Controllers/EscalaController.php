<?php

namespace App\Http\Controllers;

use App\Models\Escala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EscalaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        // Filtrar por domain_id
        $escala = Escala::where('domain_id', $domain_id)->paginate(10);
        return response()->json($escala, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validaciones
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'c' => 'nullable|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear la escala
        $escala = Escala::create($request->all());
        return response()->json(['message' => 'Escala creada correctamente', 'data' => $escala], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $escala = Escala::find($id);

        if (!$escala) {
            return response()->json(['message' => 'Escala no encontrada'], 404);
        }

        return response()->json(['data' => $escala], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validaciones
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'c' => 'nullable|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $escala = Escala::find($id);

        if (!$escala) {
            return response()->json(['message' => 'Escala no encontrada'], 404);
        }

        // Actualizar la escala
        $escala->update($request->all());
        return response()->json(['message' => 'Escala actualizada correctamente', 'data' => $escala], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $escala = Escala::find($id);

        if (!$escala) {
            return response()->json(['message' => 'Escala no encontrada'], 404);
        }

        $escala->delete();
        return response()->json(['message' => 'Escala eliminada correctamente'], 204);
    }
}
