<?php

namespace App\Http\Controllers;

use App\Models\EstadoCivil;
use Illuminate\Http\Request;

class EstadoCivilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domainId = $request->user()->domain_id;
        $estadoCivil = EstadoCivil::paginate(10);
        return response()->json($estadoCivil, 200);
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

        $estadoCivil = EstadoCivil::create($request->all());

        return response()->json(['message' => 'Estado civil creado correctamente', 'data' => $estadoCivil], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $estadoCivil = EstadoCivil::find($id);

        if (!$estadoCivil) {
            return response()->json(['message' => 'Estado civil  no encontrado'], 404);
        }

        return response()->json(['data' => $estadoCivil], 200);
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

        $estadoCivil = EstadoCivil::find($id);

        if (!$estadoCivil) {
            return response()->json(['message' => 'Estado civil no encontrado'], 404);
        }

        $estadoCivil->update($request->all());

        return response()->json(['message' => 'Estado civil actualizado correctamente', 'data' => $estadoCivil], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $estadoCivil = EstadoCivil::find($id);

        if (!$estadoCivil) {
            return response()->json(['message' => 'Estado civil no encontrado'], 404);
        }

        $estadoCivil->delete();

        return response()->json(['message' => 'Estado civil eliminado correctamente'], 204);
    }
}
