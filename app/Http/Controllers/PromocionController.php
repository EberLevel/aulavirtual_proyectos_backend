<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Promocion;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    /**
     * Muestra una lista de todas las promociones de un dominio específico.
     */
    public function index($domain_id)
    {
        // Obtener todas las promociones relacionadas con el domain_id
        $promociones = Promocion::where('domain_id', $domain_id)->paginate(10);

        return response()->json($promociones, 200);
    }

    /**
     * Guarda una nueva promoción.
     */
    public function store(Request $request)
    {
        // Usamos el validador manualmente en lugar de Request::validate()
        $validator = Validator::make($request->all(), [
            'nombre_promocion' => 'required|string|max:255',
            'descripcion' => 'string|max:255',
            'fecha_inscripcion' => 'required|date',
            'domain_id' => 'required|exists:domains,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        // Después de pasar la validación, creamos la promoción
        $promocion = Promocion::create($request->all());
    
        return response()->json(['message' => 'Promoción creada correctamente', 'data' => $promocion], 201);
    }

    /**
     * Muestra una promoción específica.
     */
    public function show($id)
    {
        // Obtener la promoción por id
        $promocion = Promocion::find($id);

        if (!$promocion) {
            return response()->json(['message' => 'Promoción no encontrada'], 404);
        }

        return response()->json($promocion, 200);
    }

    /**
     * Actualiza una promoción existente.
     */
    public function update(Request $request, $id)
    {
        // Usa Validator en lugar del método validate
        $validator = Validator::make($request->all(), [
            'nombre_promocion' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inscripcion' => 'required|date',
            'domain_id' => 'required|exists:domains,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        // Buscar la promoción por id
        $promocion = Promocion::find($id);
    
        if (!$promocion) {
            return response()->json(['message' => 'Promoción no encontrada'], 404);
        }
    
        // Actualizar la promoción
        $promocion->update([
            'nombre_promocion' => $request->nombre_promocion,
            'descripcion' => $request->descripcion,
            'fecha_inscripcion' => $request->fecha_inscripcion,
            'domain_id' => $request->domain_id,
        ]);
    
        return response()->json(['message' => 'Promoción actualizada correctamente', 'data' => $promocion], 200);
    }

    /**
     * Elimina una promoción.
     */
    public function destroy($id)
    {
        // Buscar la promoción por id
        $promocion = Promocion::find($id);

        if (!$promocion) {
            return response()->json(['message' => 'Promoción no encontrada'], 404);
        }

        // Eliminar la promoción
        $promocion->delete();

        return response()->json(['message' => 'Promoción eliminada correctamente'], 200);
    }
}
