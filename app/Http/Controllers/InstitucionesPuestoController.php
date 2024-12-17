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
            return  DB::table('area_puestos')->when($area_id, function ($query, $area_id) {
                return $query->where('area_id', $area_id);
            })->leftJoin('estado_actual', 'area_puestos.estado', '=', 'estado_actual.id')
            ->leftJoin('modalidad_puesto', 'area_puestos.modalidad_posicion', '=', 'modalidad_puesto.id')->
            select('area_puestos.*', 'estado_actual.nombre as estado', 'modalidad_puesto.nombre as modalidad_posicion')->get();
    }
    public function store(Request $request)
    {
        $id = $request->input('puesto_id');
        $puesto = $request->all();
        $rules = [
            'area_id' => 'required|exists:institucion_area,id',
            'codigo' => 'required|string|max:191',
            'nombre' => 'required|string|max:191',
            'telefono' => 'required|string|max:15',
            'email' => 'required|string|max:191',
            'salario_minimo' => 'required|numeric',
            'salario_maximo' => 'required|numeric',
            'diferencia' => 'required|numeric'
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
    }catch(\Exception $e){
            // Revierte la transacción
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar el puesto'
        .$e->getMessage()], 500);
        }
        

        return response()->json(null, 204);
    }
}
