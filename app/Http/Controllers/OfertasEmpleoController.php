<?php

namespace App\Http\Controllers;

use App\Models\OfertasEmpleo;
use Illuminate\Http\Request;

class OfertasEmpleoController extends Controller
{
    protected $domain_id;

    public function __construct(Request $request)
    {
        $this->domain_id = $request->attributes->get('domain_id');
    }

    public function index()
    {
        // Filtrar ofertas de empleo por domain_id si es necesario
        $ofertas = OfertasEmpleo::with('empleador')
            ->where('domain_id', $this->domain_id)
            ->paginate(10);
        return response()->json($ofertas, 200);
    }

    public function store(Request $request)
    {
        // Validación de los datos de entrada usando Validator
        $this->validate($request, [
            'estado' => 'required|in:PENDIENTE,INICIADO,FINALIZADO,CANCELADO,ACTIVO,PAUSADO',
            'empleador_id' => 'nullable|exists:empleadores,id',
            'plain_empleador' => 'nullable|max:191',
            'telefono' => 'required|max:20',
            'nombre_puesto' => 'required|max:100',
            'url' => 'nullable|url|max:500',
            'requisitos' => 'nullable|max:65535', // Ajustado para texto más largo
        ]);

        if (!$request->filled('empleador_id') && !$request->filled('plain_empleador')) {
            return response()->json([
                'message' => 'Debe proporcionar empleador_id o plain_empleador.'
            ], 422);
        }

        // Si se proporciona empleador_id, se asegura de que plain_empleador esté vacío
        $data = $request->only([
            'estado', 
            'empleador_id', 
            'plain_empleador', 
            'telefono', 
            'nombre_puesto', 
            'url', 
            'requisitos'
        ]);

        // Asegurarse de que el domain_id se incluya en los datos
        $data['domain_id'] = $this->domain_id;

        // Crear la oferta de empleo
        $oferta_empleo = OfertasEmpleo::create($data);

        return response()->json([
            'message' => 'Oferta de empleo creada correctamente',
            'data' => $oferta_empleo,
        ], 201);
    }

    public function show($id)
    {
        // Buscar la oferta de empleo por ID y filtrar por domain_id
        $oferta_empleo = OfertasEmpleo::with('empleador')
                                    ->where('id', $id)
                                    ->where('domain_id', $this->domain_id)
                                    ->first();

        // Si no se encuentra la oferta de empleo, retornar error 404
        if (!$oferta_empleo) {
            return response()->json(['message' => 'Oferta de empleo no encontrada'], 404);
        }

        return response()->json(['data' => $oferta_empleo], 200);
    }

    public function update(Request $request, $id)
    {

        // Validación de los datos de entrada usando Validator
        $this->validate($request, [
            'estado' => 'required|in:PENDIENTE,INICIADO,FINALIZADO,CANCELADO,ACTIVO,PAUSADO',
            'empleador_id' => 'nullable|exists:empleadores,id',
            'plain_empleador' => 'nullable|max:191',
            'telefono' => 'required|max:20',
            'nombre_puesto' => 'required|max:100',
            'url' => 'nullable|url|max:500',
            'requisitos' => 'nullable|max:65535', // Ajustado para texto más largo
        ]);

        if (!$request->filled('empleador_id') && !$request->filled('plain_empleador')) {
            return response()->json([
                'message' => 'Debe proporcionar empleador_id o plain_empleador.'
            ], 422);
        }

        // Buscar la oferta de empleo por ID y filtrar por domain_id
        $oferta_empleo = OfertasEmpleo::where('id', $id)
                                     ->where('domain_id', $this->domain_id)
                                     ->first();

        // Si no se encuentra la oferta de empleo, retornar error 404
        if (!$oferta_empleo) {
            return response()->json(['message' => 'Oferta de empleo no encontrada'], 404);
        }

        // Actualizar solo los campos permitidos
        $data = $request->only([
            'estado',
            'empleador_id',
            'plain_empleador',
            'telefono',
            'nombre_puesto',
            'url',
            'requisitos'
        ]);

        $oferta_empleo->update($data);

        return response()->json([
            'message' => 'Oferta de empleo actualizada correctamente',
            'data' => $oferta_empleo,
        ], 200);
    }

    public function destroy($id)
    {
        // Buscar la oferta de empleo por ID y filtrar por domain_id
        $oferta_empleo = OfertasEmpleo::where('id', $id)
                                     ->where('domain_id', $this->domain_id)
                                     ->first();

        // Si no se encuentra la oferta de empleo, retornar error 404
        if (!$oferta_empleo) {
            return response()->json(['message' => 'Oferta de empleo no encontrada'], 404);
        }

        // Eliminar la oferta de empleo
        $oferta_empleo->delete();

        return response()->json(['message' => 'Oferta de empleo eliminada correctamente'], 204);
    }
}