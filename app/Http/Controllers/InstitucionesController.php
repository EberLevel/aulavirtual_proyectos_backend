<?php

namespace App\Http\Controllers;

use App\Models\CvBank;
use Illuminate\Http\Request;
use App\Models\Institucion; // Asegúrate de crear el modelo correspondiente
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class InstitucionesController extends Controller
{
    // Obtener todas las instituciones
    public function index()
    {
        $domain_id = request()->query('domain_id')??null;
        $institucion_id= request()->query('institucion_id')??null;
        $user_id = request()->query('user_id')??null;
        $instituciones = Institucion::when($domain_id, function ($query, $domain_id) {
            return $query->where('domain_id', $domain_id);
        })->when(
            $institucion_id, function ($query, $institucion_id) {
            return $query->where('institucionPadre', $institucion_id);
        })->get();          
        $user_entidades=DB::table('user_entidades')->where('user_id',$user_id)->get();
        //filter instituciones where id is in $User_entidades institucion_id array
        if($user_id){
        $instituciones=$instituciones->filter(function($institucion) use ($user_entidades){
            return $user_entidades->contains('institucion_id',$institucion->id);
        });}
        
        return response()->json($instituciones->values());
    }

    // Obtener una institución por ID
    public function getInstitucion(Request $request)
    {
        $id = $request->id;
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
    public function dropdown(){
        $domain_id = request()->query('domain_id')??null;
        $instituciones = DB::table('companies')->when($domain_id, function ($query, $domain_id) {
            return $query->where('domain_id', $domain_id);
        })->select('id as value', 'name',"domain_id")->get();
        return response()->json($instituciones);
    }
    public function getPermanenciaDropdown(){
        return DB::table('puesto_permanencia')->get();
    }
    public function getContinuidadDropdown(){
        return DB::table('puesto_continuidad')->get();
    }
    //function to get all instituciones by domain_id with n object of subinstituciones
    public function getInstitucionesByDomain(){
        $domain_id = request()->query('domain_id')??null;
        $instituciones = Institucion::query()
        ->whereNull('institucionPadre')  // Trae solo las instituciones padre
        ->with(['subInstituciones' => function($query) {
            $query->orderBy('nombre', 'asc');  // Ordena las subinstituciones
        }])
        ->when($domain_id, function ($query, $domain_id) {
            return $query->where('domain_id', $domain_id);
        })
        ->orderBy('nombre', 'asc')
        ->get();
        return response()->json($instituciones);
    }
}
