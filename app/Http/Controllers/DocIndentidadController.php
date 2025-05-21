<?php

namespace App\Http\Controllers;

use App\Models\DocIdentidad;
use Illuminate\Http\Request;

class DocIndentidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domainId = $request->user()->domain_id;
        $docIdentidad = DocIdentidad::paginate(10);
        return response()->json($docIdentidad, 200);
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

        $docIdentidad = DocIdentidad::create($request->all());

        return response()->json(['message' => 'Documento de identidad creado correctamente', 'data' => $docIdentidad], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $docIdentidad = DocIdentidad::find($id);

        if (!$docIdentidad) {
            return response()->json(['message' => 'Documento de identidad  no encontrado'], 404);
        }

        return response()->json(['data' => $docIdentidad], 200);
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

        $docIdentidad = DocIdentidad::find($id);

        if (!$docIdentidad) {
            return response()->json(['message' => 'Documento de identidad no encontrado'], 404);
        }

        $docIdentidad->update($request->all());

        return response()->json(['message' => 'Documento de identidad actualizado correctamente', 'data' => $docIdentidad], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $docIdentidad = DocIdentidad::find($id);

        if (!$docIdentidad) {
            return response()->json(['message' => 'Documento de identidad  no encontrado'], 404);
        }

        $docIdentidad->delete();

        return response()->json(['message' => 'Documento de identidad  eliminado correctamente'], 204);
    }
}
