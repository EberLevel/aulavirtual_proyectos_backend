<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class InstitucionesPerfilController extends Controller
{
    public function index()
    {
        $puesto_id = request()->query('puesto_id')??null;
        return  DB::table('puesto_perfiles')->when($puesto_id, function ($query, $puesto_id) {
            return $query->where('puesto_id', $puesto_id);
        })->join('estado_actual', 'puesto_perfiles.estado', '=', 'estado_actual.id')
        ->join('modalidad_puesto', 'puesto_perfiles.modalidad_posicion', '=', 'modalidad_puesto.id')->
        select('puesto_perfiles.*', 'estado_actual.nombre as estado', 'modalidad_puesto.nombre as modalidad_posicion')->get();
    }
    public function store(Request $request)
    {
        $perfil = $request->all();
        $perfil_id = $request->input('perfil_id');
        $rules = [
            'puesto_id' => 'required|exists:institucion_area,id',
            'codigo' => 'required|string|max:191',
            'nombre' => 'required|string|max:191',
            'nivel' => 'required|string|max:191',
            'dependencia' => 'required|string|max:191',
            'nivel_perfil' => 'required|max:191',
            'sueldo_promedio' => 'required|numeric',
            'formacion' => 'required',
            'capacitacion' => 'required',
            'experiencia_especifica' => 'required',
            'experiencia_general' => 'required',
            'modalidad_posicion' => 'required',
            'estado' => 'required',
            'fecha_nombramiento' => 'required|date',
            'nombre_nombrado' => 'required|string|max:191',

        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if($perfil_id){
            unset($perfil['perfil_id']);
            DB::table('puesto_perfiles')->where('id', $perfil_id)->update($perfil);
            return response()->json($request->all(), 200);
        }
        $puesto =DB::table('puesto_perfiles')->insert($request->all());

        return response()->json($puesto, 201);
    }
    public function destroy($id)
    {
        DB::table('puesto_perfiles')->where('id', $id)->delete();
        return response()->json(null, 204);
    }
}
