<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PasswordChangeController extends Controller
{
    /**
     * Cambiar contraseña inicial
     */
    public function changeInitialPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Verificar que la contraseña actual sea correcta
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'La contraseña actual es incorrecta'
                ], 400);
            }

            // Verificar que el usuario no haya cambiado su contraseña antes
            $userRecord = DB::table('users')->where('id', $user->id)->first();
            
            if ($userRecord->password_changed) {
                return response()->json([
                    'message' => 'Ya ha cambiado su contraseña inicial'
                ], 400);
            }

            // Actualizar la contraseña y marcar como cambiada
            DB::table('users')->where('id', $user->id)->update([
                'password' => Hash::make($request->new_password),
                'password_changed' => true,
                'password_changed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return response()->json([
                'message' => 'Contraseña cambiada exitosamente',
                'password_changed' => true
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cambiar la contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si el usuario necesita cambiar su contraseña
     */
    public function checkPasswordStatus()
    {
        try {
            $user = Auth::user();
            $userRecord = DB::table('users')->where('id', $user->id)->first();
            
            return response()->json([
                'password_changed' => $userRecord->password_changed ?? false,
                'password_changed_at' => $userRecord->password_changed_at ?? null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al verificar el estado de la contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 