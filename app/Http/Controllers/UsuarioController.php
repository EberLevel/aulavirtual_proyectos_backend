<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    use UserTrait;

    public function index($domain_id)
    {
        $query = DB::table('users')
            ->select('users.id', 'users.name', 'users.email', 'rol.nombre')
            ->join('rol', 'users.rol_id', '=', 'rol.id');

        if ($domain_id && $domain_id != 2) {
            $query->where('users.domain_id', $domain_id);
        }

        $users = $query->get();
        return $users;
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'dni' => 'required|string|max:255',
            'rol_id' => 'required|integer',
            'domain_id' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            // Log de los datos recibidos
            Log::info('Datos recibidos en store:', $request->all());

            // Si id no es nulo, es una actualización
            if ($request->id) {
                $user = DB::table('users')->where('id', $request->id)->first();
                if (!$user) {
                    return response()->json(['message' => 'Usuario no encontrado'], 404);
                }
                if ($user->email != $request->email) {
                    $isValidEmail = $this->checkIsValidEmail($request->input('email'));
                    if (!$isValidEmail) {
                        return response()->json(['message' => 'Email en uso'], 400);
                    }
                }
                DB::table('users')->where('id', $request->id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'dni' => $request->dni,
                    'rol_id' => $request->rol_id,
                    'domain_id' => $request->domain_id,
                    'password' => Hash::make($request->password),
                ]);
                Log::info('Usuario actualizado con ID: ' . $request->id);

                DB::table('user_entidades')->where('user_id', $request->id)->delete();
                Log::info('Entidades existentes eliminadas para user_id: ' . $request->id);
                if ($request->entidades) {
                    $entidades = json_decode($request->entidades);
                    Log::info('Entidades recibidas para decodificar:', ['raw' => $request->entidades]);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Error al decodificar entidades: ' . json_last_error_msg());
                    }
                    foreach ($entidades as $entidad) {
                        DB::table('user_entidades')->insert([
                            'user_id' => $request->id,
                            'institucion_id' => $entidad,
                        ]);
                        Log::info('Entidad insertada:', ['user_id' => $request->id, 'institucion_id' => $entidad]);
                    }
                }

                // Eliminar permisos existentes y guardar nuevos permisos
                DB::table('user_permiso')->where('user_id', $request->id)->delete();
                Log::info('Permisos existentes eliminados para user_id: ' . $request->id);
                if ($request->permisos) {
                    $permisos = json_decode($request->permisos);
                    Log::info('Permisos recibidos para decodificar:', ['raw' => $request->permisos]);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Error al decodificar permisos: ' . json_last_error_msg());
                    }
                    foreach ($permisos as $permiso) {
                        $insertData = [
                            'user_id' => $request->id,
                            'permiso_id' => $permiso,
                            'domain_id' => $request->domain_id,
                            'proyecto_id' => $request->proyecto_id ?? null,
                        ];
                        DB::table('user_permiso')->insert($insertData);
                        Log::info('Intento de inserción en user_permiso:', $insertData);
                    }
                } else {
                    Log::info('No se recibieron permisos para user_id: ' . $request->id);
                }
            } else {
                $isValidEmail = $this->checkIsValidEmail($request->input('email'));
                if (!$isValidEmail) {
                    return response()->json(['message' => 'Email en uso'], 400);
                }

                $rolName = DB::table('rol')->where('id', $request->rol_id)->first()->nombre;
                if ($rolName == 'Docente') {
                    $docente_id = DB::table('docentes')->insertGetId([
                        'nombres' => $request->name,
                        'apellidos' => $request->lastname,
                        'email' => $request->email,
                        'dni' => $request->dni,
                        'domain_id' => $request->domain_id,
                        // Agrega otras columnas obligatorias si las hay
                    ]);
                    DB::table('users')->where('email', $request->email)->update(['docente_id' => $docente_id]);
                    Log::info('Docente creado con ID: ' . $docente_id);
                } else if ($rolName == 'Alumno') {
                    // Separar name en nombres y apellidos
                    $fullName = explode(' ', trim($request->name));
                    $nombres = $fullName[0] ?? $request->name; // Primer nombre
                    $apellidos = isset($fullName[1]) ? implode(' ', array_slice($fullName, 1)) : $request->lastname;

                    $alumnoData = [
                        'nombres' => $nombres,
                        'apellidos' => $apellidos,
                        'email' => $request->email,
                        'dni' => $request->dni,
                        'domain_id' => $request->domain_id,
                        'codigo' => 'TEMP_' . rand(1000, 9999), // Valor temporal
                        'celular' => '000-000-0000', // Valor temporal
                        'carrera_id' => 1, // Valor temporal (ajusta con un ID válido)
                        'ciclo_id' => 1, // Valor temporal (ajusta con un ID válido)
                    ];

                    $alumno_id = DB::table('alumnos')->insertGetId($alumnoData);
                    DB::table('users')->where('email', $request->email)->update(['alumno_id' => $alumno_id]);
                    Log::info('Alumno creado con ID: ' . $alumno_id);
                } else {
                    $user_id = DB::table('users')->insertGetId([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'dni' => $request->dni,
                        'rol_id' => $request->rol_id,
                        'domain_id' => $request->domain_id,
                    ]);
                    Log::info('Usuario creado con ID: ' . $user_id);

                    DB::table('user_entidades')->where('user_id', $user_id)->delete();
                    Log::info('Entidades existentes eliminadas para user_id: ' . $user_id);
                    if ($request->entidades) {
                        $entidades = json_decode($request->entidades);
                        Log::info('Entidades recibidas para decodificar:', ['raw' => $request->entidades]);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \Exception('Error al decodificar entidades: ' . json_last_error_msg());
                        }
                        foreach ($entidades as $entidad) {
                            DB::table('user_entidades')->insert([
                                'user_id' => $user_id,
                                'institucion_id' => $entidad,
                            ]);
                            Log::info('Entidad insertada:', ['user_id' => $user_id, 'institucion_id' => $entidad]);
                        }
                    }

                    // Guardar permisos del usuario
                    if ($request->permisos) {
                        $permisos = json_decode($request->permisos);
                        Log::info('Permisos recibidos para decodificar:', ['raw' => $request->permisos]);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \Exception('Error al decodificar permisos: ' . json_last_error_msg());
                        }
                        foreach ($permisos as $permiso) {
                            $insertData = [
                                'user_id' => $user_id,
                                'permiso_id' => $permiso,
                                'domain_id' => $request->domain_id,
                                'proyecto_id' => $request->proyecto_id ?? null,
                            ];
                            DB::table('user_permiso')->insert($insertData);
                            Log::info('Intento de inserción en user_permiso:', $insertData);
                        }
                    } else {
                        Log::info('No se recibieron permisos para user_id: ' . $user_id);
                    }
                }
            }
            DB::commit();
            Log::info('Transacción completada con éxito para user_id: ' . ($request->id ?? $user_id));
            return response()->json(['status' => true, 'id' => $request->id ?? $user_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Usuario no encontrado'], 404);
        }
        if ($user->rol_id == 1) {
            return response()->json(['status' => false, 'message' => 'No se puede eliminar el superadmin'], 400);
        }
        if ($user->docente_id) {
            DB::table('docentes')->where('id', $user->docente_id)->delete();
        }
        if ($user->alumno_id) {
            DB::table('alumnos')->where('id', $user->alumno_id)->delete();
        }

        DB::table('users')->where('id', $id)->delete();
        DB::table('user_permiso')->where('user_id', $id)->delete(); // Eliminar permisos del usuario
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return response()->json(['status' => true]);
    }

    public function getUser($id)
    {
        $user = DB::table('users')->where('users.id', $id)->join('rol', 'users.rol_id', '=', 'rol.id')->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Usuario no encontrado'], 404);
        }
        return json_encode($user);
    }

    function buildInstitucionTree($domain_id, $selectedIds, $parentId = null)
    {
        $items = DB::table('instituciones')
            ->where('domain_id', $domain_id)
            ->where(function ($query) use ($parentId) {
                if (is_null($parentId)) {
                    $query->whereNull('institucionPadre');
                } else {
                    $query->where('institucionPadre', $parentId);
                }
            })
            ->get();

        $result = [];

        foreach ($items as $item) {
            $item->selected = in_array($item->id, $selectedIds);
            $item->sub_instituciones = $this->buildInstitucionTree($domain_id, $selectedIds, $item->id);
            $result[] = $item;
        }

        return $result;
    }



    public function getEntidades($id)
    {
        // Obtener el usuario y su dominio
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Obtener instituciones asignadas
        $selectedIds = DB::table('user_entidades')
            ->where('user_id', $id)
            ->pluck('institucion_id')
            ->toArray();

        // Construir jerarquía completa con marcas de selección
        $tree = $this->buildInstitucionTree($user->domain_id, $selectedIds);

        return response()->json($tree);
    }


    public function getPermisos($id)
    {
        $permisos = DB::table('user_permiso')
            ->join('permiso', 'user_permiso.permiso_id', '=', 'permiso.id')
            ->select('permiso.id', 'permiso.nombre')
            ->where('user_permiso.user_id', $id)
            ->get();

        return json_encode($permisos);
    }
}
