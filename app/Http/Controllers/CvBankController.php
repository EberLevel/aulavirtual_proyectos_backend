<?php

namespace App\Http\Controllers;

use App\Models\CvBank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CvBankController extends Controller
{
    public function index(Request $request, $domain_id)
    {
        $cvBanks = CvBank::with('marital_status', 'profession', 'estadoActual', 'education_degree', 'identification_document')
            ->where('domain_id', $domain_id)
            ->byTerm($request->term)
            ->byProfessionId($request->profession_id)
            ->byEducationDegreeId($request->education_degree_id)
            ->byCurrentStateId($request->current_state_id)
            ->paginate(100000000);

        $cvBanks->getCollection()->transform(function ($cvBank) {
            try {
                $data = DB::table('cv_banks as cv')
                    ->leftJoin('area_puestos as ap', 'cv.id', '=', 'ap.cv_id')
                    ->leftJoin('institucion_area as ia', 'ap.area_id', '=', 'ia.id')
                    ->leftJoin('instituciones as i', 'ap.institucion_id', '=', 'i.id')
                    ->select(
                        'cv.estado_actual_id as estado_postulante',
                        'ap.estado as estado_puesto',
                        'ap.modalidad_posicion as color',
                        'ap.dias_restantes',
                        'ap.continuidad_id',
                        'cv.code as codigo_postulante',
                        'cv.id as postulante_id',
                        'ap.orden as n_orden',
                        'ap.nombre as objeto_orden',
                        'ap.salario_minimo as salario',
                        'ap.fecha_limite',
                        'ap.nombre as objeto_servicio',
                        'ap.sueldo_promedio as monto_total',
                        'ap.fecha_nombramiento as fecha_ingreso',
                        'ap.codigo as codigo',
                        'i.nombre as nombre_institucion',
                        'i.direccion as dependencia',
                        DB::raw("CONCAT(ap.codigo, ' - ', i.nombre) as area")
                    )
                    ->where('cv.id', $cvBank->id)
                    ->get();

                $cvBank->control_puestos = $data;
                $cvBank->image_url = $cvBank->image ? Storage::url($cvBank->image) : null;
                $cvBank->cv_url = $cvBank->cv_path ? Storage::url($cvBank->cv_path) : null;
            } catch (\Exception $e) {
                Log::error('Error fetching control puestos for CV Bank ID ' . $cvBank->id . ': ' . $e->getMessage());
                $cvBank->control_puestos = [];
                $cvBank->image_url = null;
                $cvBank->cv_url = null;
            }

            return $cvBank;
        });

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
        $count = CvBank::where('domain_id', $domain_id)->count();
        return 'CNC-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        try {
            // Log all incoming data for debugging
            Log::info('Raw POST data:', ['input' => $request->all(), 'files' => $request->files->all()]);

            // Validate input data
            $validator = Validator::make($request->all(), [
                'identification_number' => 'required|string|max:100',
                'password' => 'required|string|min:6',
                'position_code' => 'nullable|string|max:100',
                'code' => 'nullable|string|max:100',
                'identification_document_id' => 'nullable|numeric', // Cambiado a numeric para aceptar strings
                'names' => 'nullable|string|max:100',
                'phone' => 'nullable|string|max:20',
                'marital_status_id' => 'nullable|numeric',
                'number_children' => 'nullable|numeric',
                'date_birth' => 'nullable|date',
                'age' => 'nullable|numeric',
                'education_degree_id' => 'nullable|numeric',
                'profession_id' => 'nullable|numeric',
                'ocupacion_actual_id' => 'nullable|numeric',
                'email' => 'nullable|email|max:100',
                'sex' => 'nullable|string|max:10',
                'estado_actual_id' => 'nullable|numeric',
                'domain_id' => 'required|numeric|exists:domains,id', // Cambiado a numeric
                'color_id' => 'nullable|numeric',
                'link_facebook' => 'nullable|string|max:255',
                'link_instagram' => 'nullable|string|max:255',
                'link_tik_tok' => 'nullable|string|max:255',
                'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
                'nombre_imagen' => 'nullable|string|max:255',
                'cv' => 'nullable|file|mimes:pdf|max:5120',
                'nombre_cv' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                Log::error('Validation errors:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            Log::info('Validated data:', $request->all());

            // Check for existing email (if provided)
            if ($request->filled('email')) {
                $userExist = User::where('email', $request->input('email'))->first();
                if ($userExist) {
                    return response()->json(['message' => 'El correo electrónico ya está en uso'], 400);
                }
            }

            // Check for existing DNI
            $userExist = User::where('dni', $request->input('identification_number'))->first();
            if ($userExist) {
                return response()->json(['message' => 'El DNI ya está en uso'], 400);
            }

            // Handle image upload
            $imagePath = null;
            $nombreImagen = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads', 'public');
                $nombreImagen = $request->file('image')->getClientOriginalName();
                Log::info('Image uploaded:', ['path' => $imagePath, 'name' => $nombreImagen]);
            }

            // Handle CV upload
            $cvPath = null;
            $nombreCv = null;
            if ($request->hasFile('cv')) {
                $cvPath = $request->file('cv')->store('uploads', 'public');
                $nombreCv = $request->file('cv')->getClientOriginalName();
                Log::info('CV uploaded:', ['path' => $cvPath, 'name' => $nombreCv]);
            }

            // Create user
            $user = new User([
                'name' => $request->input('names'),
                'email' => $request->input('email'),
                'dni' => $request->input('identification_number'),
                'password' => Hash::make($request->input('password')),
                'domain_id' => $request->input('domain_id'),
                'rol_id' => 21,
                'type' => 'user',
                'status' => 'active',
            ]);

            $user->save();

            // Create CvBank record
            $cvBankData = [
                'position_code' => $request->input('position_code'),
                'code' => $request->input('code') ?: $this->generateCodigoConcursante($request->input('domain_id')),
                'identification_document_id' => $request->input('identification_document_id'),
                'identification_number' => $request->input('identification_number'),
                'image' => $imagePath,
                'nombre_imagen' => $nombreImagen,
                'cv_path' => $cvPath,
                'nombre_cv' => $nombreCv,
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
                'date_affiliation' => $request->input('date_affiliation') ?: date('Y-m-d'),
                'estado_actual_id' => $request->input('estado_actual_id'),
                'domain_id' => $request->input('domain_id'),
                'user_id' => $user->id,
                'color_id' => $request->input('color_id'),
                'link_facebook' => $request->input('link_facebook'),
                'link_instagram' => $request->input('link_instagram'),
                'link_tik_tok' => $request->input('link_tik_tok'),
                'urls' => $request->input('urls'),
            ];

            $cvBank = CvBank::create($cvBankData);
            Log::info('Created CvBank record:', $cvBank->toArray());

            // Update user with postulante_id
            $user->update(['postulante_id' => $cvBank->id]);

            // Add file URLs to response
            $cvBank->image_url = $cvBank->image ? Storage::url($cvBank->image) : null;
            $cvBank->cv_url = $cvBank->cv_path ? Storage::url($cvBank->cv_path) : null;

            return response()->json(['cvBank' => $cvBank], 201);
        } catch (\Exception $e) {
            Log::error('Error creating postulante: ' . $e->getMessage());
            return response()->json(['message' => 'Error creating postulante: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $cvBank = CvBank::with('marital_status', 'profession', 'estadoActual', 'education_degree', 'identification_document', 'domain')
                ->where('id', $id)
                ->first();

            if (!$cvBank) {
                return response()->json(['message' => 'Banco de CV no encontrado'], 404);
            }

            $cvBank->image_url = $cvBank->image ? Storage::url($cvBank->image) : null;
            $cvBank->cv_url = $cvBank->cv_path ? Storage::url($cvBank->cv_path) : null;

            return response()->json(['cvBank' => $cvBank], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching CV Bank ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching CV Bank: ' . $e->getMessage()], 500);
        }
    }

    public function showByUser($id)
    {
        try {
            $cvBank = CvBank::with('marital_status', 'profession', 'estadoActual', 'education_degree', 'identification_document', 'domain')
                ->where('user_id', $id)
                ->first();

            if (!$cvBank) {
                return response()->json(['message' => 'Banco de CV no encontrado para el usuario'], 404);
            }

            $cvBank->image_url = $cvBank->image ? Storage::url($cvBank->image) : null;
            $cvBank->cv_url = $cvBank->cv_path ? Storage::url($cvBank->cv_path) : null;

            return response()->json(['cvBank' => $cvBank], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching CV Bank for user ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching CV Bank: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('Raw POST data for update:', ['input' => $request->all(), 'files' => $request->files->all()]);

            $validator = Validator::make($request->all(), [
                'position_code' => 'nullable|string|max:100',
                'code' => 'required|string|max:100',
                'identification_document_id' => 'nullable|numeric',
                'identification_number' => 'required|string|max:100',
                'names' => 'nullable|string|max:100',
                'phone' => 'nullable|string|max:20',
                'marital_status_id' => 'nullable|numeric',
                'number_children' => 'nullable|numeric',
                'date_birth' => 'nullable|date',
                'age' => 'nullable|numeric',
                'education_degree_id' => 'nullable|numeric',
                'profession_id' => 'nullable|numeric',
                'ocupacion_actual_id' => 'nullable|numeric',
                'email' => 'nullable|email|max:100',
                'sex' => 'nullable|string|max:10',
                'estado_actual_id' => 'nullable|numeric',
                'domain_id' => 'required|numeric|exists:domains,id',
                'color_id' => 'nullable|numeric',
                'link_facebook' => 'nullable|string|max:255',
                'link_instagram' => 'nullable|string|max:255',
                'link_tik_tok' => 'nullable|string|max:255',
                'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
                'nombre_imagen' => 'nullable|string|max:255',
                'cv' => 'nullable|file|mimes:pdf|max:5120',
                'nombre_cv' => 'nullable|string|max:255',
                'password' => 'nullable|string|min:6',
                'urls' => 'nullable|json',
            ]);

            if ($validator->fails()) {
                Log::error('Validation errors:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cvBank = CvBank::findOrFail($id);
            $data = $request->except(['image', 'cv', 'password']);
            Log::info('Validated data for update:', $data);

            if ($request->filled('email')) {
                $userExist = User::where('email', $request->input('email'))
                    ->where('postulante_id', '!=', $cvBank->id)
                    ->first();
                if ($userExist) {
                    return response()->json(['message' => 'El correo electrónico ya está en uso'], 400);
                }
            }

            $userExist = User::where('dni', $request->input('identification_number'))
                ->where('postulante_id', '!=', $cvBank->id)
                ->first();
            if ($userExist) {
                return response()->json(['message' => 'El DNI ya está en uso'], 400);
            }

            if ($request->hasFile('image')) {
                if ($cvBank->image) {
                    Storage::disk('public')->delete($cvBank->image);
                }
                $data['image'] = $request->file('image')->store('uploads', 'public');
                $data['nombre_imagen'] = $request->file('image')->getClientOriginalName();
                Log::info('New image uploaded:', ['path' => $data['image'], 'name' => $data['nombre_imagen']]);
            }

            if ($request->hasFile('cv')) {
                if ($cvBank->cv_path) {
                    Storage::disk('public')->delete($cvBank->cv_path);
                }
                $data['cv_path'] = $request->file('cv')->store('uploads', 'public');
                $data['nombre_cv'] = $request->file('cv')->getClientOriginalName();
                Log::info('New CV uploaded:', ['path' => $data['cv_path'], 'name' => $data['nombre_cv']]);
            }

            $cvBank->update($data);
            Log::info('Updated CvBank record:', $cvBank->toArray());

            $userData = [
                'name' => $request->input('names'),
                'email' => $request->input('email'),
                'dni' => $request->input('identification_number'),
                'domain_id' => $request->input('domain_id'),
                'rol_id' => 21,
                'type' => 'user',
                'status' => 'active',
                'postulante_id' => $cvBank->id,
            ];

            if ($request->filled('password') && $request->input('password') !== '********') {
                $userData['password'] = Hash::make($request->input('password'));
            }

            $user = User::find($cvBank->user_id);
            if ($user) {
                $user->update($userData);
            } else {
                $user = new User($userData);
                $user->save();
                $cvBank->update(['user_id' => $user->id]);
            }

            $cvBank->image_url = $cvBank->image ? Storage::url($cvBank->image) : null;
            $cvBank->cv_url = $cvBank->cv_path ? Storage::url($cvBank->cv_path) : null;

            return response()->json([
                'message' => 'Banco de CV actualizado correctamente',
                'data' => $cvBank
            ], 200);
        } catch (ModelNotFoundException $e) {
            Log::error('CvBank not found: ' . $e->getMessage());
            return response()->json(['message' => 'Banco de CV no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating CvBank: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating CvBank: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cvBank = CvBank::findOrFail($id);

            if ($cvBank->image) {
                Storage::disk('public')->delete($cvBank->image);
            }
            if ($cvBank->cv_path) {
                Storage::disk('public')->delete($cvBank->cv_path);
            }

            $cvBank->delete();

            return response()->json(['message' => 'Banco de CV eliminado correctamente'], 204);
        } catch (ModelNotFoundException $e) {
            Log::error('CvBank not found: ' . $e->getMessage());
            return response()->json(['message' => 'Banco de CV no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting CvBank: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting CvBank: ' . $e->getMessage()], 500);
        }
    }
}