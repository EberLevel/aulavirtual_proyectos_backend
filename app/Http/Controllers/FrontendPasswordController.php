<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FrontendPasswordController extends Controller
{
    /**
     * Verificar token desde el frontend
     */
    public function verifyToken(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string'
        ]);

        $resetRecord = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token válido',
            'email' => $resetRecord->email
        ], 200);
    }

    /**
     * Cambiar contraseña desde el frontend
     */
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8'
        ]);

        $resetRecord = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ], 400);
        }

        // Buscar el usuario
        $user = DB::table('users')->where('email', $resetRecord->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Actualizar contraseña del usuario
        DB::table('users')
            ->where('email', $resetRecord->email)
            ->update([
                'password' => Hash::make($request->password),
                'password_changed' => true,
                'password_changed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // Eliminar el token usado
        DB::table('password_resets')
            ->where('email', $resetRecord->email)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente',
            'user_id' => $user->id
        ], 200);
    }

    /**
     * Generar URL de reset para el correo (método mejorado)
     */
    public function generateResetUrl($email)
    {
        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            return null;
        }

        // Generar token único
        $token = Str::random(60);
        
        // Guardar token en la base de datos
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Generar URL de reset (ajusta según tu frontend)
        $baseUrl = env('FRONTEND_URL', 'http://localhost:4200');
        return $baseUrl . '/change-password?token=' . $token;
    }
} 