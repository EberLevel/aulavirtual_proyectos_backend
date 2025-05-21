<?php

namespace App\Http\Controllers;

use App\Models\DocumentoGestion;
use Illuminate\Http\Request;

class DocumentoGestionController extends Controller
{
    public function index($domain_id)
    {
        $documentos = DocumentoGestion::where('estado', 1)
        ->where('domain_id', $domain_id)->
        get();
        if ($documentos) {
            return response()->json([
                'responseCode' => 0,
                'response' => $documentos
            ], 200);
        }
        return response()->json('Record not found', 404);
    }

    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:25',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'costo' => 'required|integer',
            'recursos' => 'required|string|max:255',
            'domain_id' => 'required|integer',
            'user_id' => 'required|integer'    
        ]);
    
        // Asegúrate de que estos datos existan en el request
        $request->merge(['estado' => 1]);
        $parametro = DocumentoGestion::create($request->all());
    
        return response()->json($parametro, 201);
    }

    public function getAlumnoDocuments($domain_id, $user_id)
{

    $documentos = DocumentoGestion::where('domain_id', $domain_id)
        ->where('user_id', $user_id) 
        ->where('estado', 1) 
        ->get();
        
    if ($documentos) {
        return response()->json([
            'responseCode' => 0,
            'response' => $documentos
        ], 200);
    }

    return response()->json('Record not found', 404);
}


    public function show($domain_id,$id)
    {
        $parametro = DocumentoGestion::find($id);
        if (is_null($parametro)) {
            return response()->json('Record not found', 404);
        }
        return response()->json($parametro, 200);
    }

    public function update(Request $request, DocumentoGestion $documentoGestion, $id)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:25',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'costo' => 'required|integer',
            'recursos' => 'required|string|max:255',
        ]);
        $parametro = DocumentoGestion::find($id);
        if (is_null($parametro)) {
            return response()->json('Record not found', 404);
        }
        $parametro->update($request->all());
        return response()->json($parametro, 200);
    }

    public function destroy($id)
    {
        $parametro = DocumentoGestion::find($id);
        if (is_null($parametro)) {
            return response()->json('Record not found', 404);
        }
        $parametro->estado = 0;
        $parametro->save();
        return response()->json($parametro, 200);
    }


    public function generateCode()
    {
        $count = DocumentoGestion::count() + 1;
        $codigo = 'ORD-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        return response()->json(['code' => $codigo], 200);
    }
}
