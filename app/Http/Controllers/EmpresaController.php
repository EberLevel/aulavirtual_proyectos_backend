<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Domains;
use App\Models\Empresa;
use App\Models\Rol;
use DateTime;
use Illuminate\Support\Facades\DB;
class EmpresaController extends Controller
{
    public function index()
    {
        $data = Empresa::all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'database' => 'required|string|max:255',
            'status' => 'required|numeric',
            'rol_id' => 'required',

        ]);

        $domain = new Domains();
        $domain->nombre = $request->domain;
        $domain->save();

        $empresa = Empresa::create([
            'name' => $request->name,
            'domain' => $request->domain,
            'database' => $request->database,
            'status' => $request->status,
            'rol_id' => $request->rol_id,
            'domain_id' => $domain->id
        ]);
    
        return response()->json($empresa, 201);
    }

    public function show($id)
    {
        $data = Empresa::find($id);
        return response()->json($data,200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'database' => 'required|string|max:255',
            'status' => 'required',
            'rol_id' => 'required',
        ]);
        
        $domain = Domains::find($request->domain_id);
        $domain->nombre = $request->domain;
        $domain->save();
        $empresa = Empresa::find($id);
        $empresa->name = $request->name;
        $empresa->domain = $request->domain;
        $empresa->database = $request->database;
        $empresa->status = $request->status;
        $empresa->rol_id = $request->rol_id;
        $empresa->domain_id = $domain->id;

        $empresa->save();

        return response()->json($empresa, 201);
    }

    public function destroy($id)
    {
        $empresa = Empresa::find($id);
        $empresa->delete();

        return response()->json(['mensaje' => "Se eliminó la empresa"], 200);
    }public function dropdown(){
        return DB::table('companies')->select('domain_id', 'name')->get();
     }
}
