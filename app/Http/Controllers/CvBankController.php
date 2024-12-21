<?php

namespace App\Http\Controllers;

use App\Models\CvBank\CvBank;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\bcrypt;

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
            'date_affiliation' => 'nullable|date',
            'estado_actual_id' => 'nullable|integer',
            'domain_id' => 'required|integer|exists:domains,id',
            'imagen' => 'nullable|string',
        ]);
    
        // Crear un nuevo usuario asociado con el postulante
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
    
        // Guarda el usuario en la base de datos
        $user->save();
    
        // Ahora crea el registro en la tabla `cv_banks`
        $cvBank = CvBank::create([
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
            'date_affiliation' => $request->input('date_affiliation'),
            'estado_actual_id' => $request->input('estado_actual_id'),
            'domain_id' => $request->input('domain_id'),
            'user_id' => $user->id, 
            'image' => $request->input('imagen'),
        ]);
    
        $user->update(['postulante_id' => $cvBank->id]);
    
        return response()->json(['cvBank' => $cvBank], 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cvBank = CvBank::findOrFail($id)->with('marital_status', 'profession', 'estadoActual', 'education_degree', 'identification_document')->first();
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
        $data = $this->validate($request, [
            'position_code' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'identification_document_id' => 'required|integer',
            'identification_number' => 'string|max:100',
            'image' => 'nullable|string|regex:/^data:image\/(jpeg|png|gif|bmp);base64,/',  // Validación de cadena Base64
            'names' => 'string|max:100',
            'phone' => 'nullable|string|max:20',
            'marital_status_id' => 'required|integer',
            'number_children' => 'nullable|integer',
            'date_birth' => 'date',
            'age' => 'required|integer',
            'education_degree_id' => 'required|integer',
            'profession_id' => 'nullable|integer',
            'email' => 'nullable|string|max:100'
        ]);

        $cvBank = CvBank::findOrFail($id);
        $cvBank->update($data);

        return response()->json(['message' => 'Banco de CV actualizado correctamente', 'data' => $cvBank], 200);
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
