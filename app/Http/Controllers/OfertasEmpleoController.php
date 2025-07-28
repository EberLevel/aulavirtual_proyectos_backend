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
        $ofertas = OfertasEmpleo::where('domain_id', $this->domain_id)->paginate(10);
        return response()->json($ofertas, 200);
    }

    public function store(Request $request)
    {
        // Validación de los datos de entrada usando Validator
        $this->validate($request, [
            'estado' => 'required|in:PENDIENTE,INICIADO,FINALIZADO,CANCELADO,ACTIVO,PAUSADO',
            'empresa' => 'required|max:191',
            'telefono' => 'required|max:20',
            'nombre_puesto' => 'required|max:100',
            'url' => 'nullable|url|max:500',
            'requisitos' => 'nullable|max:65535', // Ajustado para texto más largo
        ]);

        // Crear una nueva oferta de empleo con los datos proporcionados y el domain_id del constructor
        $oferta_empleo = OfertasEmpleo::create(array_merge($request->all(), ['domain_id' => $this->domain_id]));

        return response()->json([
            'message' => 'Oferta de empleo creada correctamente',
            'data' => $oferta_empleo,
        ], 201);
    }

    public function show($id)
    {
        // Buscar la oferta de empleo por ID y filtrar por domain_id
        $oferta_empleo = OfertasEmpleo::where('id', $id)
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
            'empresa' => 'required|max:191',
            'telefono' => 'required|max:20',
            'nombre_puesto' => 'required|max:100',
            'url' => 'nullable|url|max:500',
            'requisitos' => 'nullable|max:65535', // Ajustado para texto más largo
        ]);

        // Buscar la oferta de empleo por ID y filtrar por domain_id
        $oferta_empleo = OfertasEmpleo::where('id', $id)
                                     ->where('domain_id', $this->domain_id)
                                     ->first();

        // Si no se encuentra la oferta de empleo, retornar error 404
        if (!$oferta_empleo) {
            return response()->json(['message' => 'Oferta de empleo no encontrada'], 404);
        }

        // Actualizar la oferta de empleo con los nuevos datos
        $oferta_empleo->update($request->all());

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