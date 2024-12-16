<?php

namespace App\Http\Controllers;

use App\Models\EstadoActual;
use Illuminate\Http\Request;

class EstadoActualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domainId = $request->user()->domain_id;
        $TipoEstado = EstadoActual::paginate(10);
        return response()->json($TipoEstado, 200);
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

        $TipoEstado = EstadoActual::create($request->all());

        return response()->json(['message' => 'Estado creado correctamente', 'data' => $TipoEstado], 201);
    }
    /**
     * Display the specified resource.
     */


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
    public function show($id)
    {
        $TipoEstado = EstadoActual::find($id);

        if (!$TipoEstado) {
            return response()->json(['message' => 'Estado  no encontrado'], 404);
        }

        return response()->json(['data' => $TipoEstado], 200);
    }


    public function update(Request $request, $id)
    {
        $request->validate([

            'nombre' => 'required',
        ]);

        $TipoEstado = EstadoActual::find($id);

        if (!$TipoEstado) {
            return response()->json(['message' => 'Estado no encontrado'], 404);
        }

        $TipoEstado->update($request->all());

        return response()->json(['message' => 'Estado actualizado correctamente', 'data' => $TipoEstado], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $TipoEstado = EstadoActual::find($id);

        if (!$TipoEstado) {
            return response()->json(['message' => 'Estado no encontrado'], 404);
        }

        $TipoEstado->delete();

        return response()->json(['message' => 'Estado eliminado correctamente'], 204);
    }
    public function getEstadoActualDropdown()
    {   
        $domain_id = request()->query(key: 'domain_id') ?? null;
        $estados=EstadoActual::select('id', 'nombre')->get();
        return response()->json($estados, 200);
    }
}
