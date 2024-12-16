<?php

namespace App\Http\Controllers;

use App\Models\CvBank\CvBank;
use Illuminate\Http\Request;
use App\Models\Institucion; // Asegúrate de crear el modelo correspondiente
use Illuminate\Support\Facades\Validator;
    
class InstitucionesController extends Controller
{
    // Obtener todas las instituciones
    public function index()
    {
        $domain_id = request()->query('domain_id')??null;
        $institucion_id= request()->query('institucion_id')??null;
        
        $instituciones = Institucion::when($domain_id, function ($query, $domain_id) {
            return $query->where('domain_id', $domain_id);
        })->when($institucion_id, function ($query, $institucion_id) {
            return $query->where('institucionPadre', $institucion_id);
        })->get();
        return response()->json($instituciones);
    }

    // Obtener una institución por ID
    public function show($id)
    {
        $institucion = Institucion::find($id);
        if ($institucion) {
            return response()->json($institucion);
        }
        return response()->json(['message' => 'Institución no encontrada'], 404);
    }

    // Crear una nueva institución
    public function store(Request $request)
    {
        // Define las reglas de validación
        $rules = [
            'codigo' => 'required|string|max:191',
            'nivel' => 'required|string|max:191',
            'siglas' => 'required|string|max:191',
            'nombre' => 'required|string|max:191',
            'ubigeo' => 'required|string|max:6',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:15',
            'domain_id' => 'required|exists:domains,id'
        ];

        // Aplica la validación
        $validator = Validator::make($request->all(), $rules);

        // Verifica si la validación falla
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Si la validación es exitosa, crea la nueva institución
        $institucion = Institucion::create($request->all());

        return response()->json($institucion, 201);
    }

    // Actualizar una institución
    public function update(Request $request, $id)
    {
        // Define las reglas de validación
        $rules = [
            'codigo' => 'required|string|max:191',
            'nivel' => 'required|string|max:191',
            'siglas' => 'required|string|max:191',
            'nombre' => 'required|string|max:191',
            'ubigeo' => 'required|string|max:6',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:15',
            'domain_id' => 'required|exists:domains,id'
        ];

        // Aplica la validación
        $validator = Validator::make($request->all(), $rules);

        // Verifica si la validación falla
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Busca la institución por su ID
        $institucion = Institucion::find($id);
        
        // Si la institución no existe, devolver un error 404
        if (!$institucion) {
            return response()->json(['message' => 'Institución no encontrada'], 404);
        }

        // Actualizar los datos de la institución
        $institucion->update($request->all());

        return response()->json($institucion, 200);
    }

    // Eliminar una institución
    public function destroy($id)
    {
        $institucion = Institucion::find($id);
        if (!$institucion) {
            return response()->json(['message' => 'Institución no encontrada'], 404);
        }

        $institucion->delete();
        return response()->json(['message' => 'Institución eliminada']);
    }
    public function getCv(){
        $document_number = request()->query('document_number')??null;
        $domain_id = request()->query('domain_id')??null;
        return CvBank::when($document_number, function ($query, $document_number) {
            return $query->where('identification_number', $document_number);
        })->when($domain_id, function ($query, $domain_id) {
            return $query->where('domain_id', $domain_id);
        })->get();
    }
}
