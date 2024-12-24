<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Traits\UserTrait;

class UsuarioController extends Controller
{
    use UserTrait;
    public function index($domain_id){
        if(!$domain_id){
            $users=DB::table('users')->select('users.id','name','email','nombre')->join('rol','users.rol_id','=','rol.id')->get();
        }else{
            $users=DB::table('users')->select('users.id','name','email','nombre')->join('rol','users.rol_id','=','rol.id')->where('domain_id',$domain_id)->get();
        }
        if($domain_id == 0){
            $users=DB::table('users')->select('users.id','name','email','nombre')->join('rol','users.rol_id','=','rol.id')->get();
        }
        return $users;
    }
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'dni' => 'required|string|max:255',
            'rol_id' => 'required|integer',
            'domain_id' => 'required|integer',
        ]);
        DB::beginTransaction();
       try{
         //if id is not null, then it is an update
         if($request->id){
            $user=DB::table('users')->where('id',$request->id)->first();
            if(!$user){
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
            if($user->email!=$request->email){
                $isValidEmail=$this->checkIsValidEmail($request->input('email'));
                if(!$isValidEmail){
                    return response()->json(['message' => 'Email en uso'], 400);
                }
            }
            DB::table('users')->where('id',$request->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'dni' => $request->dni,
                'rol_id' => $request->rol_id,
                'domain_id' => $request->domain_id,
                'password' => Hash::make($request->password),
            ]);
            DB::table(table: 'user_entidades')->where('user_id',$request->id)->delete();
            if($request->entidades){
                $entidades=json_decode($request->entidades);
                foreach($entidades as $entidad){
                    DB::table('user_entidades')->insert([
                        'user_id' => $request->id,
                        'institucion_id' => $entidad,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status'=>true]);
        }
        $isValidEmail=$this->checkIsValidEmail($request->input('email'));
        if(!$isValidEmail){
            return response()->json(['message' => 'Email en uso'], 400);
        }

        $rolName=DB::table('rol')->where('id',$request->rol_id)->first()->nombre;
        if($rolName=='Docente'){
            $docente_id=DB::table('docentes')->insertGetId([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'dni' => $request->dni,
                'domain_id' => $request->domain_id,
            ]);
            DB::table('users')->where('email',$request->email)->update(['docente_id'=>$docente_id]);
        }else if($rolName=='Alumno'){
            $alumno_id=DB::table('alumnos')->insertGetId([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'dni' => $request->dni,
                'domain_id' => $request->domain_id,
            ]);
            DB::table('users')->where('email',$request->email)->update(['alumno_id'=>$alumno_id]);
        }else{
            $user_id=DB::table('users')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'dni' => $request->dni,
                'rol_id' => $request->rol_id,
                'domain_id' => $request->domain_id,
            ]);
            DB::table(table: 'user_entidades')->where('user_id',$user_id)->delete();
            if($request->entidades){
                $entidades=json_decode($request->entidades);
                foreach($entidades as $entidad){
                    DB::table('user_entidades')->insert([
                        'user_id' => $user_id,
                        'institucion_id' => $entidad,
                    ]);
                }
            }
        }
        DB::commit();
        return response()->json(['status'=>true]);
       }
       catch(\Exception $e){
              DB::rollBack();
           return response()->json(['message' => $e->getMessage()], 400);
       }

    }
    public function destroy($id){
        //set foreign key check to 0
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $user=DB::table('users')->where('id',$id)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Usuario no encontrado'],404);
        }
        if($user->rol_id==1){
            return response()->json(['status'=>false,'message'=>'No se puede eliminar el superadmin'],400);
        }
        if($user->docente_id){
            DB::table('docentes')->where('id',$user->docente_id)->delete();
        }
        if($user->alumno_id){
            DB::table('alumnos')->where('id',$user->alumno_id)->delete();
        }

        DB::table('users')->where('id',$id)->delete();
        //set foreign key check to 1
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return response()->json(['status'=>true]);
    }
    public function getUser($id){
        $user=DB::table('users')->where('users.id',$id)->join('rol','users.rol_id','=','rol.id')->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Usuario no encontrado'],404);
        }
        return json_encode($user);
    }
    public function getEntidades($id){
        $entidades=DB::table('user_entidades')->
        join('instituciones','user_entidades.institucion_id','=','instituciones.id')->
        select('instituciones.id','instituciones.nombre')->
        where('user_id',$id)->get();
        return json_encode($entidades);
    }
}
