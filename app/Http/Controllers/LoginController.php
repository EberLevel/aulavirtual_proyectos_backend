<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class LoginController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|max:255',
            'email' => 'nullable|string|max:255', // Opcional, se usa solo si se pasa
            'dni' => 'nullable|string|max:191' // Opcional, se usa solo si se pasa
        ]);
    
        if (!$request->has('email') && !$request->has('dni')) {
            return response()->json(['mensaje' => 'Debe proporcionar un correo electrónico o un DNI para iniciar sesión', 'status' => 422], 200);
        }
    
        $userQuery = DB::table('users');
        
        if ($request->has('email')) {
            $userQuery->where('email', $request->email);
        } elseif ($request->has('dni')) {
            $userQuery->where('dni', $request->dni);
        }
        
        $user = $userQuery->first();
    
        // Si no se encuentra el usuario, retorna un error
        if (!$user) {
            return response()->json(['mensaje' => 'Usuario no encontrado', 'status' => 404], 200);
        }
    
        // Verifica la contraseña
        if (Hash::check($request->password, $user->password)) {
            // Verifica si el usuario es un alumno (rol_id = 12), docente (rol_id = 17) o postulante (rol_id = 21) y si ha cambiado su contraseña inicial
          /*  if ((($user->rol_id == 12) || ($user->rol_id == 17) || ($user->rol_id == 21)) && !$user->password_changed) {
                return response()->json([
                    'mensaje' => 'Debe cambiar su contraseña inicial antes de continuar',
                    'status' => 403,
                    'require_password_change' => true,
                    'user_id' => $user->id,
                    'email' => $user->email
                ], 200);
            }*/
            
            // Genera un token de API
            $apiToken = Str::random(150);
    
            // Actualiza al usuario con el nuevo token
            DB::table('users')->where('id', $user->id)->update(['api_token' => $apiToken]);
    
            // Añade el token al objeto del usuario
            $user->api_token = $apiToken;
    
            return response()->json(['mensaje' => 'Usuario autenticado', 'status' => 200, 'user' => $user], 200);
        } else {
            return response()->json(['mensaje' => 'Contraseña incorrecta', 'status' => 404], 200);
        }
    }
    

    public function logout(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required|string',
        ]);

        // Invalidate the API token
        $user = DB::table('users')->where('api_token', $request->api_token)->first();
        if ($user) {
            DB::table('users')->where('id', $user->id)->update(['api_token' => null]);
            return response()->json(['mensaje' => 'Usuario desconectado', 'status' => 200], 200);
        } else {
            return response()->json(['mensaje' => 'Token inválido', 'status' => 404], 200);
        }
    }
}
