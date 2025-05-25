<?php

namespace App\Http\Controllers;

use App\Models\CvBank;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CvBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $domain_id)
    {
        $cvBanks = CvBank::with('marital_status', 'profession', 'estadoActual', 'education_degree', 'identification_document')
            ->where('domain_id', $domain_id)
            ->byTerm($request->term)
            ->byProfessionId($request->profession_id)
            ->byEducationDegreeId($request->education_degree_id)
            ->byCurrentStateId($request->current_state_id)
            ->paginate(10);

        return response()->json($cvBanks, 200);
    }

    public function filtersData()
    {
        $data = [
            'education_degrees' => \App\Models\GradoInstruccion::all(),
            'professions' => \App\Models\Profesion::all(),
            'current_states' => \App\Models\EstadoActual::all()
        ];

        return response()->json($data, 200);
    }

    public function dataCreate($domain_id)
    {
        $code = $this->generateCodigoConcursante($domain_id);
        $data = [
            'code' => $code,
            'identification_documents' => \App\Models\DocIdentidad::where('domain_id', $domain_id)->get(),
            'marital_statuses' => \App\Models\EstadoCivil::all(),
            'education_degrees' => \App\Models\GradoInstruccion::where('domain_id', $domain_id)->get(),
            'professions' => \App\Models\Profesion::where('domain_id', $domain_id)->get(),
            'current_states' => \App\Models\EstadoActual::all(),
            'position_levels' => \App\Models\NivelCargo::where('domain_id', $domain_id)->get(),
            'scales' => \App\Models\Escala::where('domain_id', $domain_id)->get(),
            'actions' => \App\Models\AccionOi::where('domain_id', $domain_id)->get(),
            'training_types' => \App\Models\TipoCapacitacion::where('domain_id', $domain_id)->get(),
            'ocupacion_actual' => \App\Models\OcupacionActual::where('domain_id', $domain_id)->get(),
        ];

        return response()->json($data, 200);
    }

    private function generateCodigoConcursante($domain_id)
    {
        $count = \App\Models\CvBank::where('domain_id', $domain_id)->count();
        return 'CNC-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'identification_number' => 'required|string|max:100',
                'password' => 'required|string|min:6',
                'position_code' => 'nullable|string|max:100',
                'code' => 'nullable|string|max:100',
                'identification_document_id' => 'nullable|integer',
                'names' => 'nullable|string|max:100',
                'phone' => 'nullable|string|max:20',
                'marital_status_id' => 'nullable|integer',
                'number_children' => 'nullable|integer',
                'date_birth' => 'nullable|date',
                'age' => 'nullable|integer',
                'education_degree_id' => 'nullable|integer',
                'profession_id' => 'nullable|integer',
                'ocupacion_actual_id' => 'nullable|integer', // Asegura que este campo sea permitido
                'email' => 'nullable|email|max:100',
                'sex' => 'nullable|string|max:1',
                'estado_actual_id' => 'nullable|integer',
                'domain_id' => 'required|integer|exists:domains,id',
                'color_id' => 'nullable|integer',
                'link_facebook' => 'nullable|string|max:255',
                'link_instagram' => 'nullable|string|max:255',
                'link_tik_tok' => 'nullable|string|max:255',
                'image' => 'nullable|string',
                'nombre_archivo' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                Log::error('Errores de validación:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            Log::info('Datos recibidos en store:', $request->all());

            // Verificar usuarios existentes (omitir si email es null)
            if ($request->filled('email')) {
                $userExist = \App\Models\User::where('email', $request->input('email'))->first();
                if ($userExist) {
                    return response()->json(['message' => 'El correo electrónico ya está en uso'], 400);
                }
            }

            // Verificar unicidad de DNI
            $userExist = \App\Models\User::where('dni', $request->input('identification_number'))->first();
            if ($userExist) {
                return response()->json(['message' => 'El DNI ya está en uso'], 400);
            }

            // Validar la cadena base64 si existe
            if ($request->filled('image')) {
                $base64Image = $request->input('image');
                Log::info('Procesando imagen base64:', ['image_length' => strlen($base64Image)]);

                // Verificar si la cadena base64 es válida
                if (base64_decode($base64Image, true) === false) {
                    Log::error('Cadena base64 inválida');
                    return response()->json(['message' => 'La cadena base64 de la imagen no es válida'], 400);
                }
            } else {
                Log::info('No se recibió ninguna imagen en la solicitud');
            }

            // Crear usuario
            $user = new \App\Models\User([
                'name' => $request->input('names'),
                'email' => $request->input('email'),
                'dni' => $request->input('identification_number'),
                'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
                'domain_id' => $request->input('domain_id'),
                'rol_id' => 21,
                'type' => 'user',
                'status' => 'active',
            ]);

            $user->save();

            // Crear registro en cv_banks
            $cvBankData = [
                'position_code' => $request->input('position_code'),
                'code' => $request->input('code') ?: $this->generateCodigoConcursante($request->input('domain_id')),
                'identification_document_id' => $request->input('identification_document_id'),
                'identification_number' => $request->input('identification_number'),
                'names' => $request->input('names'),
                'phone' => $request->input('phone'),
                'marital_status_id' => $request->input('marital_status_id'),
                'number_children' => $request->input('number_children'),
                'date_birth' => $request->input('date_birth'),
                'age' => $request->input('age'),
                'education_degree_id' => $request->input('education_degree_id'),
                'profession_id' => $request->input('profession_id'),
                'ocupacion_actual_id' => $request->input('ocupacion_actual_id'), // Asegura que se incluya
                'email' => $request->input('email'),
                'sex' => $request->input('sex'),
                'date_affiliation' => date('Y-m-d'),
                'estado_actual_id' => $request->input('estado_actual_id'),
                'domain_id' => $request->input('domain_id'),
                'user_id' => $user->id,
                'color_id' => $request->input('color_id'),
                'link_facebook' => $request->input('link_facebook'),
                'link_instagram' => $request->input('link_instagram'),
                'link_tik_tok' => $request->input('link_tik_tok'),
            ];

            // Asignar la cadena base64 al campo image si existe
            if ($request->filled('image')) {
                $cvBankData['image'] = $request->input('image');
                Log::info('Cadena base64 asignada a cvBankData:', ['image_length' => strlen($cvBankData['image'])]);
            }

            $cvBank = CvBank::create($cvBankData);
            Log::info('Registro creado en cv_banks:', $cvBank->toArray());

            // Actualizar relación con el usuario
            $user->update(['postulante_id' => $cvBank->id]);

            return response()->json(['cvBank' => $cvBank], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear postulante: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear el postulante: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cvBank = DB::table('cv_banks')
            ->where('cv_banks.id', $id)
            ->leftJoin('domains', 'cv_banks.domain_id', '=', 'domains.id')
            ->leftJoin('estado_civil', 'cv_banks.marital_status_id', '=', 'estado_civil.id')
            ->leftJoin('grado_instruccion', 'cv_banks.education_degree_id', '=', 'grado_instruccion.id')
            ->leftJoin('estado_actual', 'cv_banks.estado_actual_id', '=', 'estado_actual.id')
            ->leftJoin('doc_identidad', 'cv_banks.identification_document_id', '=', 'doc_identidad.id')
            ->select(
                'cv_banks.*',
                'domains.nombre as domain',
                'estado_civil.nombre as marital_status',
                'grado_instruccion.nombre as education_degree',
                'estado_actual.nombre as estado_actual',
                'doc_identidad.nombre as identification_document'
            )
            ->first();

        // Convert image to Base64 for frontend
        if ($cvBank && $cvBank->image) {
            $imagePath = base_path('public/' . $cvBank->image); // Use base_path for Lumen
            if (file_exists($imagePath)) {
                $cvBank->image = base64_encode(file_get_contents($imagePath));
                $cvBank->nombre_archivo = basename($cvBank->image);
            } else {
                $cvBank->image = null;
                $cvBank->nombre_archivo = null;
            }
        }

        return response()->json(['cvBank' => $cvBank]);
    }

    public function showByUser($id)
    {
        $cvBank = CvBank::where('user_id', $id)->first();

        // Convert image to Base64 for frontend
        if ($cvBank && $cvBank->image) {
            $imagePath = base_path('public/' . $cvBank->image); // Use base_path for Lumen
            if (file_exists($imagePath)) {
                $cvBank->image = base64_encode(file_get_contents($imagePath));
                $cvBank->nombre_archivo = basename($cvBank->image);
            } else {
                $cvBank->image = null;
                $cvBank->nombre_archivo = null;
            }
        }

        return response()->json(['cvBank' => $cvBank]);
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
    try {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'position_code' => 'nullable|string|max:100',
            'code' => 'required|string|max:100',
            'identification_document_id' => 'nullable|integer',
            'identification_number' => 'required|string|max:100',
            'names' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'marital_status_id' => 'nullable|integer',
            'number_children' => 'nullable|integer',
            'date_birth' => 'nullable|date',
            'age' => 'nullable|integer',
            'education_degree_id' => 'nullable|integer',
            'profession_id' => 'nullable|integer',
            'ocupacion_actual_id' => 'nullable|integer',
            'email' => 'nullable|email|max:100',
            'sex' => 'nullable|string|max:1',
            'estado_actual_id' => 'nullable|integer',
            'domain_id' => 'required|integer|exists:domains,id',
            'color_id' => 'nullable|integer',
            'link_facebook' => 'nullable|string|max:255',
            'link_instagram' => 'nullable|string|max:255',
            'link_tik_tok' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'nombre_archivo' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            Log::error('Errores de validación:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cvBank = CvBank::findOrFail($id);
        // Excluir 'image' y 'password' desde el inicio
        $data = $request->except(['image', 'password']);
        Log::info('Datos recibidos en update (sin image ni password):', $data);

        // Validar unicidad de email (omitir si es null)
        if ($request->filled('email')) {
            $userExist = \App\Models\User::where('email', $request->input('email'))
                ->where('postulante_id', '!=', $cvBank->id)
                ->first();
            if ($userExist) {
                return response()->json(['message' => 'El correo electrónico ya está en uso'], 400);
            }
        }

        // Validar unicidad de DNI
        $userExist = \App\Models\User::where('dni', $request->input('identification_number'))
            ->where('postulante_id', '!=', $cvBank->id)
            ->first();
        if ($userExist) {
            return response()->json(['message' => 'El DNI ya está en uso'], 400);
        }

        // Validar la cadena base64 si existe y no es vacía
        if ($request->has('image') && $request->input('image') !== '') {
            $base64Image = $request->input('image');
            Log::info('Procesando imagen base64 en update:', ['image_length' => strlen($base64Image)]);

            // Verificar si la cadena base64 es válida
            if (base64_decode($base64Image, true) === false) {
                Log::error('Cadena base64 inválida en update');
                return response()->json(['message' => 'La cadena base64 de la imagen no es válida'], 400);
            }
            $data['image'] = $base64Image; // Asignar la nueva cadena base64
            Log::info('Cadena base64 asignada a cvBankData en update:', ['image_length' => strlen($base64Image)]);
        } else {
            // Conservar la imagen existente si no se envía una nueva o es vacía
            Log::info('No se recibió nueva imagen válida, conservando la existente', ['image_value' => $request->input('image')]);
        }

        // Actualizar CvBank
        $cvBank->update($data);
        Log::info('Registro actualizado en cv_banks:', $cvBank->toArray());

        // Actualizar o crear usuario asociado
        $userData = [
            'name' => $request->input('names'),
            'email' => $request->input('email'),
            'dni' => $request->input('identification_number'),
            'domain_id' => $request->input('domain_id'),
            'rol_id' => 21,
            'type' => 'user',
            'status' => 'active',
            'postulante_id' => $cvBank->id
        ];

        // Actualizar la contraseña solo si se proporciona y no es el placeholder
        if ($request->filled('password') && $request->input('password') !== '********') {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->input('password'));
        }

        $user = \App\Models\User::find($cvBank->user_id);
        if ($user) {
            $user->update($userData);
        } else {
            $user = new \App\Models\User($userData);
            $user->save();
        }

        return response()->json([
            'message' => 'Banco de CV actualizado correctamente',
            'data' => $cvBank
        ], 200);
    } catch (ModelNotFoundException $e) {
        Log::error('Banco de CV no encontrado: ' . $e->getMessage());
        return response()->json(['message' => 'Banco de CV no encontrado'], 404);
    } catch (\Exception $e) {
        Log::error('Error al actualizar el banco de CV: ' . $e->getMessage());
        return response()->json(['message' => 'Error al actualizar el banco de CV: ' . $e->getMessage()], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cvBank = CvBank::find($id);

        if (!$cvBank) {
            return response()->json(['message' => 'Banco de CV no encontrado'], 404);
        }

        $cvBank->delete();

        return response()->json(['message' => 'Banco de CV eliminado correctamente'], 204);
    }
}