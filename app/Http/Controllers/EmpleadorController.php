<?php

namespace App\Http\Controllers;

use App\Models\Empleador;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Hash;

class EmpleadorController extends Controller
{
    // Crear un nuevo empleador
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'status' => 'required|numeric',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        \DB::beginTransaction();
        try {
            // Crear usuario con rol 14 por defecto
            $user_id = \DB::table('users')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol_id' => 53,
                'domain_id' => $request->domain_id,
            ]);

            // Crear empleador con el user_id generado
            $empleador = Empleador::create([
                'name' => $request->name,
                'status' => $request->status,
                'user_id' => $user_id,
                'domain_id' => $request->domain_id,
            ]);

            \DB::commit();
            return response()->json([
                'message' => 'Empleador y usuario creados correctamente',
                'data' => $empleador,
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // Obtener empleador por id de usuario
    public function getByUserId($user_id)
    {
        $empleador = Empleador::where('user_id', $user_id)->first();

        if (!$empleador) {
            return response()->json(['message' => 'Empleador no encontrado'], 404);
        }

        return response()->json(['data' => $empleador], 200);
    }
}