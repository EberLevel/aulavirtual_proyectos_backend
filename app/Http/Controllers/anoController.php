<?php

namespace App\Http\Controllers;

use App\Models\Ano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        // Obtener todos los años filtrados por domain_id
        $anos = Ano::where('domain_id', $domain_id)->paginate(10);
        return response()->json($anos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de campos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|numeric|exists:domains,id', // Validar que el domain_id exista
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Crear un nuevo año
        $ano = Ano::create($request->all());

        return response()->json(['message' => 'Año creado correctamente', 'data' => $ano], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Obtener un año por su ID
        $ano = Ano::find($id);

        if (!$ano) {
            return response()->json(['message' => 'Año no encontrado'], 404);
        }

        return response()->json(['data' => $ano], 200);
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

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Buscar el año por su ID
        $ano = Ano::find($id);

        if (!$ano) {
            return response()->json(['message' => 'Año no encontrado'], 404);
        }

        // Actualizar el año
        $ano->update($request->all());

        return response()->json(['message' => 'Año actualizado correctamente', 'data' => $ano], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Buscar el año por su ID
        $ano = Ano::find($id);

        if (!$ano) {
            return response()->json(['message' => 'Año no encontrado'], 404);
        }

        // Eliminar el año
        $ano->delete();

        return response()->json(['message' => 'Año eliminado correctamente'], 200);
    }
}
