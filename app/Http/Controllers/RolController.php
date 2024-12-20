<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Rol;
use DateTime;
use Illuminate\Support\Facades\DB;
use App\Traits\CommonTrait;

class RolController extends Controller
{
    use CommonTrait;
    public function index($domain_id)
    {
        $data = Rol::all();
        if($domain_id=="null"){
            return response()->json($data);
        }else{
            //filteer permise with id 1 not allowed
            $data = Rol::where('id','!=',1)->get();
            
        }
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
        ]);

        $rol = Rol::create([
            'nombre' => $request->nombre,
            'fecha' => new DateTime()
        ]);

        return response()->json($rol, 201);
    }

    public function show($id)
    {
        $data = Rol::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
        ]);

        $rol = Rol::find($id);
        $rol->nombre = $request->get('nombre');
        $rol->save();
        return response()->json($rol, 201);
    }

    public function destroy($id)
    {
        $rol = Rol::find($id);
        $rol->permisos()->detach();
        $rol->delete();

        return response()->json(['mensaje' => "Se eliminó el rol"], 200);
    }

    public function guardarPermiso(Request $request)
{
    $idRol = $request->get('id');
    $idPermisos = $request->get('idPermisos', null);
    $idDomain =$request['domain_id'];
    
    // Elimina los permisos existentes
    DB::table('rol_permiso')->where('idrol', $idRol)->
    where('domain_id', $idDomain)->
    delete();
    // Inserta los nuevos permisos
    if ($idPermisos) {
        $data = [];
        foreach ($idPermisos as $idpermiso) {
            if ($idpermiso['permiso_id'] !== null) {
                 $data[] = ['idrol' => $idRol, 'idpermiso' => $idpermiso['permiso_id'], 'domain_id' => $idDomain, 'proyecto_id' => $idpermiso['proyecto_id']];
            }   
        }
            DB::table('rol_permiso')->insert($data);
    }

    return response()->json(['mensaje' => "Se registraron los permisos"], 201);
}

    public function getRolPermisos($id, $domain_id)
    {
        if($domain_id=="null"){
            $rol = DB::table('permiso')
            ->join('rol_permiso', 'permiso.id', '=', 'rol_permiso.idpermiso')
            ->where('rol_permiso.idrol', $id)
            ->select('permiso.id', 'permiso.nombre', 'rol_permiso.proyecto_id')
            ->get();
            return response()->json($rol);
        }
        $rol = DB::table('permiso')
            ->join('rol_permiso', 'permiso.id', '=', 'rol_permiso.idpermiso')
            ->where('rol_permiso.idrol', $id)
            ->where('rol_permiso.domain_id', $domain_id)
            ->select('permiso.id', 'permiso.nombre', 'rol_permiso.proyecto_id')
            ->get();
            //filter permiso with id 1
            
            return response()->json($rol);
        
    }
    public function getRolesDropDown()
    {
        $roles = $this->getRolesDropDownTrait();
        return response()->json($roles);
    }
}
