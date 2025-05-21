<?php

namespace App\Http\Controllers;

use App\Models\Institucione;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class InstitucioneController extends Controller
{
    public function index(){
        $institucione = Institucione::all();
        return response()->json($institucione);
    }
    public function store(Request $request){
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'siglas' => 'required|string|max:255',
            'logotipo' => 'string|max:255',
        ]);
        $institucione = Institucione::create($request->all());
        return response()->json($institucione, 201);
    }
    public function show($id){
        $instituciones = Institucione::find($id);
        if(!$instituciones){
            return response()->json("Record not found");
        }
        return response()->json($instituciones);
    }
    public function update(Request $request, $id){
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'siglas' => 'required|string|max:255',
            'logotipo' => 'string|max:255',
        ]);
        $instituciones = Institucione::find($id);
        if(!$instituciones){
            return response()->json("Record not found");
        }
        $instituciones->update($request->all());
        return response()->json($instituciones, 200);
    }
    public function destroy($id){
        $instituciones = Institucione::find($id);
        if(!$instituciones){
            return response()->json("Record not found");
        }
        $instituciones->delete();
        return response()->json(["message" => "Eliminadod correctamente"], 204);
    }
    public function dropdown(){
       return DB::table('companies')->select('domain_id', 'name')->get();
    }
}
