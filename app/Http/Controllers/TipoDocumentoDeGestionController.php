<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumentoDeGestion;
use Illuminate\Http\Request;

class TipoDocumentoDeGestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposDocumentos = TipoDocumentoDeGestion::paginate(10);

        return response()->json($tiposDocumentos, 200);
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

        $tipoDocumento = TipoDocumentoDeGestion::create($request->all());

        return response()->json(['message' => 'Documento de gestión creado correctamente', 'data' => $tipoDocumento], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tipoDocumento = TipoDocumentoDeGestion::find($id);

        if (!$tipoDocumento) {
            return response()->json(['message' => 'Documento de gestión no encontrado'], 404);
        }

        return response()->json(['data' => $tipoDocumento], 200);
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

        $tipoDocumento = TipoDocumentoDeGestion::find($id);

        if (!$tipoDocumento) {
            return response()->json(['message' => 'Documento de gestión no encontrado'], 404);
        }

        $tipoDocumento->update($request->all());

        return response()->json(['message' => 'Documento de gestión actualizado correctamente', 'data' => $tipoDocumento], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tipoDocumento = TipoDocumentoDeGestion::find($id);

        if (!$tipoDocumento) {
            return response()->json(['message' => 'Documento de gestión no encontrado'], 404);
        }

        $tipoDocumento->delete();

        return response()->json(['message' => 'Documento de gestión eliminado correctamente'], 204);
    }
}
