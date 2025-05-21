<?php

namespace App\Http\Controllers;

use App\Models\ModalidadPuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModalidadPuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        // Filtrar por domain_id
        $modalidadPuesto = ModalidadPuesto::where('domain_id', $domain_id)->paginate(10);
        return response()->json($modalidadPuesto, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Usar Validator en lugar de $request->validate()
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|integer|exists:domains,id', // Verificar que el domain_id exista en la tabla domains
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);  // Retornar errores si la validación falla
        }

        // Crear el ModalidadPuesto
        $modalidadPuesto = ModalidadPuesto::create($request->all());

        return response()->json(['message' => 'Modalidad de puesto creado correctamente', 'data' => $modalidadPuesto], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Obtener una ModalidadPuesto por su ID
        $modalidadPuesto = ModalidadPuesto::find($id);

        if (!$modalidadPuesto) {
            return response()->json(['message' => 'Modalidad de puesto no encontrado'], 404);
        }

        return response()->json(['data' => $modalidadPuesto], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos con Validator
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'required|integer|exists:domains,id',  // Asegurar que el domain_id exista
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $modalidadPuesto = ModalidadPuesto::find($id);

        if (!$modalidadPuesto) {
            return response()->json(['message' => 'Modalidad de puesto no encontrado'], 404);
        }

        // Actualizar la ModalidadPuesto
        $modalidadPuesto->update($request->all());

        return response()->json(['message' => 'Modalidad de puesto actualizado correctamente', 'data' => $modalidadPuesto], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Buscar ModalidadPuesto por ID
        $modalidadPuesto = ModalidadPuesto::find($id);

        if (!$modalidadPuesto) {
            return response()->json(['message' => 'Modalidad de puesto no encontrado'], 404);
        }

        // Eliminar ModalidadPuesto
        $modalidadPuesto->delete();

        return response()->json(['message' => 'Modalidad de puesto eliminado correctamente'], 204);
    }
    public function getDropdown()
    {
        $domain_id = request()->query(key: 'domain_id') ?? null;
        $modalidadPuesto = ModalidadPuesto::when($domain_id, function ($query, $domain_id) {
            return $query->where('domain_id', $domain_id);
        })->select('id', 'nombre')->get();

        return response()->json($modalidadPuesto, 200);
    }
}
