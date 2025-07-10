<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $user = Auth::user();
        
        // Verificar si el usuario es un alumno (rol_id = 12) y si ha cambiado su contraseña
        $userRecord = DB::table('users')->where('id', $user->id)->first();
        
        if ($userRecord && $userRecord->rol_id == 12 && !$userRecord->password_changed) {
            return response()->json([
                'message' => 'Debe cambiar su contraseña antes de continuar',
                'require_password_change' => true,
                'user_id' => $user->id
            ], 403);
        }

        return $next($request);
    }
} 