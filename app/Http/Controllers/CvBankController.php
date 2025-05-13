<?php

namespace App\Http\Controllers;

use App\Models\CvBank;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\bcrypt;
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
            ->where('domain_id', $domain_id) // Filtrar por domain_id
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
            'ocupacion_actual' => \App\Models\OcupacionActual::where('domain_id', $domain_id)->get(), // Nueva línea para ocupacion_actual
        ];

        return response()->json($data, 200);
    }

    private function generateCodigoConcursante($domain_id)
    {
        $count = \App\Models\CvBank\CvBank::where('domain_id', $domain_id)->count();
        return 'CNC-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'identification_number' => 'required|string|max:100', // DNI es obligatorio
            'password' => 'required|string|min:6', // Password es obligatorio
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
            'ocupacion_actual_id' => 'nullable|integer',
            'email' => 'nullable|string|max:100',
            'sex' => 'nullable|string|max:1',
            'estado_actual_id' => 'nullable|integer',
            'domain_id' => 'required|integer|exists:domains,id',
            'imagen' => 'nullable|string', // Cambiado para base64
            'nombre_archivo' => 'nullable|string|max:255', // Para el nombre del archivo
        ]);

        try {
            // Verificar usuarios existentes
            $userExist = \App\Models\User::where('email', $request->input('email'))->first();
            if ($userExist) {
                return response()->json(['message' => 'El correo electrónico ya está en uso'], 400);
            }

            $userExist = \App\Models\User::where('dni', $request->input('identification_number'))->first();
            if ($userExist) {
                return response()->json(['message' => 'El DNI ya está en uso'], 400);
            }

            // Procesar la imagen base64 si existe
            $imagePath = null;
            if ($request->filled('imagen')) {
                // Obtener datos base64 y nombre del archivo
                $base64Image = $request->input('imagen');
                $fileName = $request->input('nombre_archivo') ?: 'profile-' . time() . '.jpg';

                // Generar nombre único de archivo
                $imageName = time() . '-' . str_replace(' ', '-', $fileName);

                // Definir directorio
                $directory = 'cv_banks';
                $uploadPath = public_path('uploads/' . $directory);

                // Crear directorio si no existe
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Guardar archivo
                $filePath = $uploadPath . '/' . $imageName;
                file_put_contents($filePath, base64_decode($base64Image));

                // Guardar ruta relativa
                $imagePath = 'uploads/' . $directory . '/' . $imageName;
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
                'code' => $request->input('code'),
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
                'ocupacion_actual_id' => $request->input('ocupacion_actual_id'),
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

            // Añadir la ruta de la imagen si existe
            if ($imagePath) {
                $cvBankData['image'] = $imagePath;
            }

            $cvBank = CvBank::create($cvBankData);

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
        $cvBank = DB::table('cv_banks')->where('cv_banks.id', $id)->leftJoin('domains', 'cv_banks.domain_id', '=', 'domains.id')->leftJoin('estado_civil', 'cv_banks.marital_status_id', '=', 'estado_civil.id')->leftJoin('grado_instruccion', 'cv_banks.education_degree_id', '=', 'grado_instruccion.id')->leftJoin('estado_actual', 'cv_banks.estado_actual_id', '=', 'estado_actual.id')->leftJoin('doc_identidad', 'cv_banks.identification_document_id', '=', 'doc_identidad.id')->select('cv_banks.*', 'domains.nombre as domain', 'estado_civil.nombre as marital_status', 'grado_instruccion.nombre as education_degree', 'estado_actual.nombre as estado_actual', 'doc_identidad.nombre as identification_document')->first();
        return response()->json(['cvBank' => $cvBank]);
    }

    public function showByUser($id)
    {
        $cvBank = CvBank::where('user_id', $id)->first();

        return response()->json(['cvBank' => $cvBank]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Modify validation rules for base64 image
            $validator = Validator::make($request->all(), [
                'position_code' => 'required|string|max:100',
                'code' => 'required|string|max:100',
                'identification_document_id' => 'required|integer',
                'identification_number' => 'string|max:100',
                'names' => 'string|max:100',
                'phone' => 'nullable|string|max:20',
                'marital_status_id' => 'required|integer',
                'number_children' => 'nullable|integer',
                'date_birth' => 'date',
                'age' => 'required|integer',
                'education_degree_id' => 'required|integer',
                'profession_id' => 'nullable|integer',
                'email' => 'nullable|string|max:100',
                'color_id' => 'nullable|integer',
                'link_facebook' => 'nullable|string|max:255',
                'link_instagram' => 'nullable|string|max:255',
                'link_tik_tok' => 'nullable|string|max:255',
                'imagen' => 'nullable|string', // Changed to string for base64
                'nombre_archivo' => 'nullable|string|max:255', // For the filename
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cvBank = CvBank::findOrFail($id);
            $data = $request->except(['imagen', 'nombre_archivo', 'password']);

            // Process base64 image if it exists
            if ($request->filled('imagen')) {
                // Get base64 string and file name
                $base64Image = $request->input('imagen');
                $fileName = $request->input('nombre_archivo') ?: 'profile-' . time() . '.jpg';

                // Generate a unique file name
                $imageName = time() . '-' . str_replace(' ', '-', $fileName);

                // Define the directory
                $directory = 'cv_banks';
                $uploadPath = public_path('uploads/' . $directory);

                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Save the file to diskg
                $filePath = $uploadPath . '/' . $imageName;
                file_put_contents($filePath, base64_decode($base64Image));

                // Save the relative path to database
                $data['image'] = 'uploads/' . $directory . '/' . $imageName;
            }

            // Validar email único
            $userExist = \App\Models\User::where('email', $request->input('email'))
                ->where('postulante_id', '!=', $cvBank->id)
                ->first();
            if ($userExist) {
                return response()->json(['message' => 'El correo electrónico ya está en uso'], 400);
            }

            // Validar DNI único
            $userExist = \App\Models\User::where('dni', $request->input('identification_number'))
                ->where('postulante_id', '!=', $cvBank->id)
                ->first();
            if ($userExist) {
                return response()->json(['message' => 'El DNI ya está en uso'], 400);
            }

            $cvBank->update($data);

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

            // Solo actualizar password si se proporcionó
            if ($request->filled('password')) {
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
