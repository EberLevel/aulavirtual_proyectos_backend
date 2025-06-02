<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Rol;
use DateTime;
use Illuminate\Support\Facades\DB;
use App\Traits\CommonTrait;
use App\Models\Permiso;
use Illuminate\Support\Facades\Log;

class RolController extends Controller
{
    use CommonTrait;

    public function index($domain_id, $rol_id)
    {
        Log::info('👉 domain_id: ' . $domain_id);
        Log::info('👉 rol_id: ' . $rol_id);

        // Superadmin (rol_id == 1) ve todos los roles
        if ((int)$rol_id === 1) {
            return response()->json(Rol::all());
        }

        // Roles del dominio actual + algunos sin dominio (id 12, 17, 21)
        $roles = Rol::where(function ($query) use ($domain_id) {
                        $query->where('domain_id', $domain_id)
                            ->orWhere(function ($q) {
                                $q->whereNull('domain_id')
                                    ->whereIn('id', [12, 17, 21]);
                            });
                    })
                    ->whereNotIn('id', [1, 8])
                    ->get();

        Log::info('🔎 Roles finales (con dominio ' . $domain_id . ' y globales 12,17,21): ' . $roles->count());

        return response()->json($roles);
    }
 
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'domain_id' => 'nullable|integer|exists:domains,id' 
        ]);
    
        $rol = Rol::create([
            'nombre' => $request->nombre,
            'fecha' => new DateTime(),
            'domain_id' => $request->domain_id
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

    public function getRolPermisosAdmin($id,$domain_id){
        $permisos= Permiso::withRecursiveSelectedForAdmin($id, $domain_id)->get();
        return response()->json($permisos, 200);
    }
    public function getRolPermisosAdminDominio($id,$domain_id){
        $permisos= Permiso::withRecursivePermissions(8,$id, $domain_id);
        return response()->json($permisos, 200);
    }
    public function getRolPermisos($id, $domain_id)
    {
        $domain_id=="null"?$domain_id=null:$domain_id;
        $permisos= Permiso::withRecursiveSelected($id, $domain_id);

        return response()->json($permisos, 200);
        // $rol = DB::table('permiso')
        //     ->join('rol_permiso', 'permiso.id', '=', 'rol_permiso.idpermiso')
        //     ->where('rol_permiso.idrol', $id)
        //     ->where('rol_permiso.domain_id', $domain_id)
        //     ->select('permiso.id', 'permiso.nombre', 'rol_permiso.proyecto_id')
        //     ->get();
            //filter permiso with id 1
            
            return response()->json($rol);
        
    }
    public function getRolesDropDown($domain_id, $rol_id)
    {
        if ((int)$rol_id === 1) {
            // Superadmin puede ver todos los roles
            return response()->json(Rol::all());
        }
    
        // Usuarios normales solo ven roles de su dominio (y excluye rol ID 1 si deseas)
        $roles = Rol::where('domain_id', $domain_id)
                    ->where('id', '!=', 1)
                    ->get();
    
        return response()->json($roles);
    }
    
}
