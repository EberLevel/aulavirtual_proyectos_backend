<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class InstitucionAreaController extends Controller
{
    public function index(){
        $institucion_id= request()->query('institucion_id')??null;
        $area_padre_id= request()->query('area_id')??null;
        return  DB::table('institucion_area')
        ->when($institucion_id, function ($query, $institucion_id) {
            return $query->where('institucion_id', $institucion_id);
        })->
        when($area_padre_id, function ($query, $area_padre_id) {
            return $query->where('area_padre_id', $area_padre_id);
        })->get();
    }
    public function store(Request $request){
        $institucion_area = $request->all();
        $id=$request->input('id');
        if($id){
            //remove id from array
            unset($institucion_area['id']);
            DB::table('institucion_area')->where('id', $id)->update($institucion_area);
            return response()->json($institucion_area, 200);
        }
        DB::table('institucion_area')->insert($institucion_area);
        return response()->json($institucion_area, 201);
    }
    public function destroy($id){
        //init transaction
        DB::beginTransaction();
        try{
            $puestos=DB::table('area_puestos')->where('area_id', $id)->get();
            foreach($puestos as $puesto){
                DB::table('puesto_perfiles')->where('puesto_id', $puesto->id)->delete();
                DB::table('area_puestos')->where('id', $puesto->id)->delete();
            }
            DB::table('institucion_area')->where('area_padre_id', $id)->delete();
            DB::table('institucion_area')->where('id', $id)->delete();
            DB::commit();
        }catch(\Exception $e){
            //rollback transaction
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar el área'
        .$e->getMessage()], 500);
        }
        

        return response()->json(null, 204);
    }
    public function getByAreaId($id){
        return DB::table('institucion_area')->where('id', $id)->get();
    }
}
