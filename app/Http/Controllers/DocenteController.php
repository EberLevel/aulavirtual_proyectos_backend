<?php

namespace App\Http\Controllers;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Traits\FileTrait;
use App\Traits\UserTrait;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class DocenteController extends Controller
{
    use FileTrait, UserTrait;

    public function index($domain_id)
    {
        $docentes = DB::table('docentes as d')
            ->select('d.*')
            ->where('d.domain_id', $domain_id)
            ->get();
        
        foreach ($docentes as $docente) {
            if ($docente->foto) {
                $docente->foto = 'data:image/jpeg;base64,' . $docente->foto;
            } else {
                // Manejar el caso donde la foto no existe
                $docente->foto = null;
            }
        }
        
        return response()->json(['Exito' => true, 'Datos' => $docentes], 200);
    }
    
    public function getLoggedDocente($docente_id, $dominio) {
        $docente = Docente::leftJoin('domains', 'domains.id', '=', 'docentes.domain_id')
            ->select(
                'docentes.*',
                'domains.nombre as institucion'
            )
            ->where('docentes.id', $docente_id)
            ->where('docentes.domain_id', $dominio)
            ->first();
    
        if ($docente) {
            // Si la foto existe, añade el prefijo base64
            if ($docente->foto) {
                $docente->foto = 'data:image/jpeg;base64,' . $docente->foto;
            }
            return response()->json($docente);
        }
    
        return response()->json('Docente no encontrado', 404);
    }    
    
    public function imagen(Request $request)
    {
        $base64Image = $request->input('base64');
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            return response()->json(['Error' => true, 'Mensaje' => 'Imagen inválida']);
        }
        $imageType = $matches[1];
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $image = base64_decode($base64Image);
        
        // Generar un nombre único para el archivo
        $imageName = uniqid() . '.' . $imageType;

        // Definir la ruta donde se guardará la imagen
        $imagePath = storage_path('app\\public\\docentes\\' . $imageName);

        // Crear el directorio si no existe
        if (!file_exists(dirname($imagePath))) {
            mkdir(dirname($imagePath), 0777, true);
        }

        // Guardar la imagen en el disco
        file_put_contents($imagePath, $image);
        return response()->json($imagePath);
    }

    public function show($domain_id, $id)
    {
        // Asegúrate de filtrar también por el domain_id
        $docente = Docente::select('id', 'codigo', 'nombres', 'celular', 'profesion', 'tipo_documento', 'doc_identidad', 'fecha_nacimiento', 'genero', 'foto', 'roles', 'email')
                           ->where('id', $id)
                           ->where('domain_id', $domain_id) // Filtro por dominio
                           ->first();
    
        if (!$docente) {
            return response()->json(['Error' => 'Docente no encontrado'], 404);
        }
    
        return response()->json(['Exito' => true, 'Datos' => $docente], 200);
    }
    
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar los campos de entrada
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:20',
                'nombres' => 'required|string|max:200',
                'contraseña' => 'required|string|max:30',
                'celular' => 'required|string|max:20',
                'profesion' => 'required|string|max:200',
                'tipo_documento' => 'required|string|max:20',
                'doc_identidad' => 'required|string|max:20',
                'fecha_nacimiento' => 'required|date|before:today',
                'genero' => 'required|string|max:100',
                'foto' => 'nullable|string', // La foto es opcional y debe ser una cadena base64
                'roles' => 'required|string|max:100',
                'email' => 'required|email',
                'domain_id' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 422);
            }
    
            // Procesar la imagen base64
            $imageBase64 = $request->input('foto');
            $imagePath = null;
    
            if ($imageBase64) {
                // Verifica si la imagen está en formato base64
                if (preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $matches)) {
                    $imageType = $matches[1]; // Obtener el tipo de imagen (jpeg, png, etc.)
                    $imageBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageBase64);
                    $image = base64_decode($imageBase64);
    
                    // Puedes optar por almacenar la imagen directamente en Base64 o convertir a formato adecuado
                    // Guarda la cadena base64 en la base de datos
                    $imagePath = $imageBase64;
                }
            }
            $docenteRol = DB::table('rol')->where('nombre', 'Docentes')->first();
            
            if (!$docenteRol) {
                return response()->json(['Error' => true, 'Mensaje' => 'El rol "Docentes" no existe en la base de datos.'], 400);
            }
            // Guardar el docente
            $docenteData = [
                'codigo' => $request->codigo,
                'nombres' => $request->nombres,
                'celular' => $request->celular,
                'profesion' => $request->profesion,
                'tipo_documento' => $request->tipo_documento,
                'doc_identidad' => $request->doc_identidad,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'foto' => $imagePath, // La imagen en formato Base64
                'roles' => $request->roles,
                'email' => $request->email,
                'domain_id' => $request->domain_id
            ];
    
            $docenteId = DB::table('docentes')->insertGetId($docenteData);
    
            // Guardar también los datos del usuario
            $userData = [
                'name' => $request->nombres,
                'email' => $request->email,
                'password' => Hash::make($request->contraseña),
                'rol_id' => $docenteRol->id,
                'domain_id' => $request->domain_id,
                'docente_id' => $docenteId
            ];
            DB::table('users')->insert($userData);
    
            DB::commit();
            return response()->json(['Exito' => true, 'Mensaje' => 'Registro exitoso'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['Error' => true, 'Mensaje' => $e->getMessage()], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Verificar si el docente existe
            $docente = Docente::find($id);
            if (!$docente) {
                return response()->json(['Error' => 'Docente no encontrado'], 404);
            }
    
            // Validar los campos de entrada
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:20',
                'nombres' => 'required|string|max:200',
                'clave' => 'nullable|string|max:30', // Clave es opcional
                'celular' => 'required|string|max:20',
                'profesion' => 'required|string|max:30',
                'tipo_documento' => 'required|string|max:20',
                'doc_identidad' => 'required|string|max:20|unique:docentes,doc_identidad,' . $id,
                'fecha_nacimiento' => 'required|date|before:today',
                'genero' => 'required|string|max:100',
                'roles' => 'required|string|max:100',
                'email' => 'required|email',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 422);
            }
    
            // Procesar la imagen si se envía una nueva
            $imageBase64 = $request->input('foto');
            if ($imageBase64) {
                // Verifica si la imagen está en formato base64
                if (preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $matches)) {
                    $imageType = $matches[1]; // Obtener el tipo de imagen (jpeg, png, etc.)
                    $imageBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageBase64);
                    $image = base64_decode($imageBase64);
    
                    // Guarda la cadena base64 en la base de datos
                    $docente->foto = $imageBase64;
                }
            }
    
            // Actualizar los datos del docente
            $docente->update([
                "codigo" => $request->codigo,
                "nombres" => $request->nombres,
                "celular" => $request->celular,
                "profesion" => $request->profesion,
                "tipo_documento" => $request->tipo_documento,
                "doc_identidad" => $request->doc_identidad,
                "fecha_nacimiento" => $request->fecha_nacimiento,
                "genero" => $request->genero,
                "roles" => $request->roles,
                'email' => $request->email,
                // No actualizar "foto" si no se envía una nueva imagen
            ]);
    
            // Actualizar los datos correspondientes en la tabla users
            $userData = [
                'name' => $request->nombres,
                'email' => $request->email,
                'domain_id' => $request->domain_id, // Usa el domain_id si es relevante en la actualización
                'docente_id' => $id,
            ];
    
            // Solo actualizar la contraseña si se envía una nueva
            if ($request->filled('clave')) {
                $userData['password'] = Hash::make($request->clave);
            }
    
            // Actualiza la tabla users donde el docente_id coincide
            DB::table('users')->where('docente_id', $id)->update($userData);
    
            DB::commit();
            return response()->json(['Exito' => true, 'Mensaje' => 'Docente y usuario actualizados correctamente'], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['Error' => true, 'Mensaje' => $e->getMessage()], 500);
        }
    }
    
    
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Elimina primero los registros relacionados en la tabla `users`
            DB::table('users')->where('docente_id', $id)->delete();
    
            // Luego elimina el docente
            $docente = Docente::find($id);
            if(!$docente) {
                DB::rollBack();
                return response()->json(['Error' => 'Docente no encontrado'], 404);
            }
            $docente->delete();
    
            DB::commit();
            return response()->json(['Mensaje' => 'Docente eliminado'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }
    
    public function dropDown($domain_id){
        $docentes = Docente::select('id', 'nombres')->where('domain_id', $domain_id)->get();
        return response()->json($docentes);
    }
}
