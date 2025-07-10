<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Verificar token de reset
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
            return response()->json(['message' => 'Token inválido o expirado'], 400);
        }

        return response()->json([
            'message' => 'Token válido',
            'email' => $resetRecord->email
        ], 200);
    }

    /**
     * Cambiar contraseña
     */
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        $resetRecord = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if (!$resetRecord) {
            return response()->json(['message' => 'Token inválido o expirado'], 400);
        }

        // Actualizar contraseña del usuario
        DB::table('users')
            ->where('email', $resetRecord->email)
            ->update([
                'password' => Hash::make($request->password),
                'updated_at' => Carbon::now()
            ]);

        // Eliminar el token usado
        DB::table('password_resets')
            ->where('email', $resetRecord->email)
            ->delete();

        return response()->json(['message' => 'Contraseña actualizada exitosamente'], 200);
    }

    /**
     * Generar URL de reset para el correo
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
        $baseUrl = env('FRONTEND_URL', 'http://localhost:3000');
        return $baseUrl . '/reset-password?token=' . $token;
    }
} 