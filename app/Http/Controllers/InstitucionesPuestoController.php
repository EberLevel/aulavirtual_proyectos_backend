<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto; // Asegúrate de crear el modelo correspondiente
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InstitucionesPuestoController extends Controller
{
    public function index()
    {
            $area_id = request()->query('area_id')??null;
            return  DB::table('area_puestos')->
            join('institucion_area', 'area_puestos.area_id', '=', 'institucion_area.id')->
            join('instituciones', 'institucion_area.institucion_id', '=', 'instituciones.id')->
            leftJoin('puestos_estado', 'area_puestos.estado', '=', 'puestos_estado.id')
            ->leftJoin('modalidad_puesto', 'area_puestos.modalidad_posicion', '=', 'modalidad_puesto.id')->
            leftJoin('puesto_continuidad', 'area_puestos.continuidad_id', '=', 'puesto_continuidad.id')->
            leftJoin('puesto_permanencia', 'area_puestos.permanencia_id', '=', 'puesto_permanencia.id')->
            where('institucion_area.institucion_id', $area_id)->
            select('area_puestos.*', 'puestos_estado.nombre as estado', 'modalidad_puesto.nombre as modalidad_posicion',
            'puesto_continuidad.nombre as continuidad', 'puesto_permanencia.nombre as permanencia','institucion_area.nombre as area',
            'instituciones.siglas as institucion_siglas'
            )->orderByRaw('COALESCE(area_puestos.orden, 9999999) ASC')->
            get();
    }
    public function getPuestosByDomain(){
        $domain_id = request()->query('domain_id')??null;
        $puestos = DB::table('area_puestos')->join('institucion_area', 'area_puestos.area_id', '=', 'institucion_area.id')->
        join('instituciones', 'institucion_area.institucion_id', '=', 'instituciones.id')->
        leftJoin('puestos_estado', 'area_puestos.estado', '=', 'puestos_estado.id')
        ->leftJoin('modalidad_puesto', 'area_puestos.modalidad_posicion', '=', 'modalidad_puesto.id')->
        leftJoin('puesto_continuidad', 'area_puestos.continuidad_id', '=', 'puesto_continuidad.id')->
        leftJoin('puesto_permanencia', 'area_puestos.permanencia_id', '=', 'puesto_permanencia.id')->
        where('instituciones.domain_id', $domain_id)->
        select('area_puestos.*',
        'instituciones.siglas as institucion_siglas',
         'puestos_estado.nombre as estado',
         'modalidad_puesto.nombre as modalidad_posicion',
        'institucion_area.nombre as area', 'instituciones.nombre as institucion',
        'puesto_continuidad.nombre as continuidad', 'puesto_permanencia.nombre as permanencia'
        )
        ->orderByRaw('COALESCE(area_puestos.orden, 9999999) ASC')->
        get();
        return response()->json($puestos);
    }
    public function store(Request $request)
    {
        $id = $request->input('puesto_id');
        $puesto = $request->all();
        $rules = [
            'codigo' => 'required|string|max:191',
            'nombre' => 'required|string|max:191',
            'telefono' => 'sometimes|string|max:15',
            'salario_minimo' => 'sometimes|numeric',
            'salario_maximo' => 'sometimes|numeric',
            'diferencia' => 'sometimes|numeric',
            'orden' => 'sometimes|regex:/^[0-9]+(\.[0-9]+)*$/'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if($id){
            unset($puesto['puesto_id']);
            DB::table('area_puestos')->where('id', $id)->update($puesto);
            return response()->json($request->all(), 200);
        }
        // Si la validación es exitosa, crea el nuevo puesto
        $puesto =DB::table('area_puestos')->insert($request->all());

        return response()->json($puesto, 201);
    }
    public function destroy($id)
    {
        // Inicia la transacción
        DB::beginTransaction();
        try{
            DB::table('area_puestos')->where('id', $id)->
            update(['codigo' =>null, 'nombre' =>null, 'telefono' =>null, 'email' =>null, 
        'formacion' =>null, 'capacitacion' =>null, 'experiencia_especifica' =>null, 'experiencia_general' =>null,
    'fecha_nombramiento' =>null, 'nombre_nombrado' =>null, 'estado' =>null, 'modalidad_posicion' =>null,
            'sueldo_promedio' =>null, 'nivel' =>null, 'dependencia' =>null, 'nivel_perfil' =>null, 
]);
            DB::commit();
    }
    catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar el puesto'
        .$e->getMessage()], 500);
        }   
        

        return response()->json(null, 204);
    }
    public function getLastId(){
        $puesto = DB::table('area_puestos')->orderBy('id', 'desc')->first();
        //concat to 6 digits 
        $id = str_pad($puesto->id + 1, 6, "0", STR_PAD_LEFT);
        return response()->json($id);
    }
}
