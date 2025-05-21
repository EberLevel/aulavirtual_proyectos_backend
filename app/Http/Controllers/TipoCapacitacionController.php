<?php

namespace App\Http\Controllers;

use App\Models\TipoCapacitacion;
use Illuminate\Http\Request;

class TipoCapacitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipoCapacitacion = TipoCapacitacion::paginate(10);

        return response()->json($tipoCapacitacion, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'nombre' => 'required',
        ]);

        $tipoCapacitacion = TipoCapacitacion::create($request->all());

        return response()->json(['message' => 'tipo de capacitación creado correctamente', 'data' => $tipoCapacitacion], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tipoCapacitacion = TipoCapacitacion::find($id);

        if (!$tipoCapacitacion) {
            return response()->json(['message' => 'tipo de capacitación no encontrado'], 404);
        }

        return response()->json(['data' => $tipoCapacitacion], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([

            'nombre' => 'required',
        ]);

        $tipoCapacitacion = TipoCapacitacion::find($id);

        if (!$tipoCapacitacion) {
            return response()->json(['message' => 'tipo de capacitación no encontrado'], 404);
        }

        $tipoCapacitacion->update($request->all());

        return response()->json(['message' => 'tipo de capacitación actualizado correctamente', 'data' => $tipoCapacitacion], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tipoCapacitacion = TipoCapacitacion::find($id);

        if (!$tipoCapacitacion) {
            return response()->json(['message' => 'tipo de capacitación no encontrado'], 404);
        }

        $tipoCapacitacion->delete();

        return response()->json(['message' => 'tipo de capacitación eliminado correctamente'], 204);
    }
}
