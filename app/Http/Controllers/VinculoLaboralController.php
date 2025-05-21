<?php

namespace App\Http\Controllers;

use App\Models\VinculoLaboral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VinculoLaboralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        // Obtener los vínculos laborales filtrados por domain_id
        $vinculoLaboral = VinculoLaboral::where('domain_id', $domain_id)->paginate(10);

        return response()->json($vinculoLaboral, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de campos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id', // Verificar que el domain_id exista
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Creación de un nuevo vínculo laboral
        $vinculoLaboral = VinculoLaboral::create($request->all());

        return response()->json([
            'message' => 'Vínculo laboral creado correctamente',
            'data' => $vinculoLaboral,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Obtener un vínculo laboral por ID
        $vinculoLaboral = VinculoLaboral::find($id);

        if (!$vinculoLaboral) {
            return response()->json([
                'message' => 'Vínculo laboral no encontrado',
            ], 404);
        }

        return response()->json([
            'data' => $vinculoLaboral,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validación de campos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id',
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Buscar el vínculo laboral por ID
        $vinculoLaboral = VinculoLaboral::find($id);

        if (!$vinculoLaboral) {
            return response()->json([
                'message' => 'Vínculo laboral no encontrado',
            ], 404);
        }

        // Actualizar el vínculo laboral
        $vinculoLaboral->update($request->all());

        return response()->json([
            'message' => 'Vínculo laboral actualizado correctamente',
            'data' => $vinculoLaboral,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Buscar el vínculo laboral por ID
        $vinculoLaboral = VinculoLaboral::find($id);

        if (!$vinculoLaboral) {
            return response()->json([
                'message' => 'Vínculo laboral no encontrado',
            ], 404);
        }

        // Eliminar el vínculo laboral
        $vinculoLaboral->delete();

        return response()->json([
            'message' => 'Vínculo laboral eliminado correctamente',
        ], 200);
    }
}
