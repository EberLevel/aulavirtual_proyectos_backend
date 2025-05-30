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
        $rules = [
            'codigo' => 'required|string|max:191',
            'nivel' => 'required|string|max:191',
            'siglas' => 'required|string|max:191',
            'nombre' => 'required|string|max:191',
            'telefono' => 'required|string|max:15',
            'domain_id' => 'required|exists:domains,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $institucion = Institucion::create($request->all());

        return response()->json($institucion, 201);
    }

    // Actualizar una institución
    public function update(Request $request, $id)
    {
        $rules = [
            'codigo' => 'required|string|max:191',
            'nivel' => 'required|string|max:191',
            'siglas' => 'required|string|max:191',
            'nombre' => 'required|string|max:191',
            'telefono' => 'required|string|max:15',
            'domain_id' => 'required|exists:domains,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $institucion = Institucion::find($id);
        
        if (!$institucion) {
            return response()->json(['message' => 'Institución no encontrada'], 404);
        }

        $institucion->update($request->all());

        return response()->json($institucion, 200);
    }

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

    public function getInstitucionesByDomain(){
        $domain_id = request()->query('domain_id')??null;
        $instituciones = Institucion::query()
        ->whereNull('institucionPadre') 
        ->with(['subInstituciones' => function($query) {
            $query->orderBy('nombre', 'asc');  
        }])
        ->when($domain_id, function ($query, $domain_id) {
            return $query->where('domain_id', $domain_id);
        })
        ->orderBy('nombre', 'asc')
        ->get();
        return response()->json($instituciones);
    }

    public function massiveUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_id' => 'required|exists:domains,id',
            'instituciones' => 'required|array',
            'instituciones.*.codigo' => 'required|string|max:191',
            'instituciones.*.nivel' => 'required|string|max:191',
            'instituciones.*.siglas' => 'required|string|max:191',
            'instituciones.*.nombre' => 'required|string|max:191',
            'instituciones.*.direccion' => 'required|string',
            'instituciones.*.institucionPadre' => 'nullable|string|max:191', // Validamos como string (código)
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $domain_id = $request->input('domain_id');
        $instituciones = $request->input('instituciones');
        $success_count = 0;
        $errors = [];

        foreach ($instituciones as $index => $institucion) {
            try {
                // Buscar el ID de la institución padre usando el código
                $parentId = null;
                if (!empty($institucion['institucionPadre'])) {
                    $parentInstitucion = Institucion::where('codigo', $institucion['institucionPadre'])
                        ->where('domain_id', $domain_id)
                        ->first();
                    if (!$parentInstitucion) {
                        throw new \Exception('Institución padre con código "' . $institucion['institucionPadre'] . '" no encontrada');
                    }
                    $parentId = $parentInstitucion->id;
                }

                // Crear la institución
                Institucion::create([
                    'codigo' => $institucion['codigo'],
                    'nivel' => $institucion['nivel'],
                    'siglas' => $institucion['siglas'],
                    'nombre' => $institucion['nombre'],
                    'ubigeo' => '000000', // Valor por defecto
                    'direccion' => $institucion['direccion'],
                    'telefono' => '000000000', // Valor por defecto
                    'domain_id' => $domain_id,
                    'institucionPadre' => $parentId,
                ]);

                $success_count++;
            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $index + 1,
                    'errors' => [$e->getMessage()],
                ];
            }
        }

        $response = [
            'success_count' => $success_count,
            'errors' => $errors,
        ];

        return response()->json($response, 200);
    }
}