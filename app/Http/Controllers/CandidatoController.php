<?php

namespace App\Http\Controllers;

use App\Models\Candidato; // Importa el modelo 'Candidato'
use App\Models\User; // Importa el modelo 'User'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class CandidatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $domain_id)
    {
        // Obtener todos los candidatos solo filtrando por domain_id
        $candidatos = Candidato::where('domain_id', $domain_id)->get();
    
        return response()->json($candidatos, 200);
    }
    

    public function getCiudadByCandidato($id)
    {
        $candidato = Candidato::findOrFail($id); // Buscar el candidato por su ID

        // Verificar si el candidato tiene un 'ciudad_id'
        if (!$candidato->ciudad_id) {
            return response()->json(['message' => 'El candidato no tiene una ciudad asociada.'], 404);
        }

        return response()->json(['ciudad_id' => $candidato->ciudad_id], 200);
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
        $code = $this->generateCodigoCandidato($domain_id);
        $data = [
            'code' => $code,
            'identification_documents' => \App\Models\DocIdentidad::where('domain_id', $domain_id)->get(),
            'marital_statuses' => \App\Models\EstadoCivil::all(),
            'education_degrees' => \App\Models\GradoInstruccion::where('domain_id', $domain_id)->get(),
            'professions' => \App\Models\Profesion::where('domain_id', $domain_id)->get(),
            'current_states' => \App\Models\EstadoActual::where('domain_id', $domain_id)->get(),
            'position_levels' => \App\Models\NivelCargo::where('domain_id', $domain_id)->get(),
            'scales' => \App\Models\Escala::where('domain_id', $domain_id)->get(),
            'actions' => \App\Models\AccionOi::where('domain_id', $domain_id)->get(),
            'training_types' => \App\Models\TipoCapacitacion::where('domain_id', $domain_id)->get(),
            'ocupacion_actual' => \App\Models\OcupacionActual::where('domain_id', $domain_id)->get(),
        ];

        return response()->json($data, 200);
    }

    private function generateCodigoCandidato($domain_id)
    {
        $count = Candidato::where('domain_id', $domain_id)->count();
        return 'CND-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'identification_number' => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
            'code' => 'nullable|string|max:100',
            'identification_document_id' => 'nullable|integer',
            'apaterno' => 'nullable|string|max:100',
            'amaterno' => 'nullable|string|max:100',
            'nombre' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'marital_status_id' => 'nullable|integer',
            'puesto' => 'nullable|integer',
            'date_birth' => 'nullable|date',
            'age' => 'nullable|integer',
            'education_degree_id' => 'nullable|integer',
            'profesion' => 'nullable|string|max:191',  // Modificado a string
            'ocupacion_actual' => 'nullable|string|max:191',  // Modificado a string
            'email' => 'nullable|string|max:100',
            'sex' => 'nullable|string|max:1',
            'date_affiliation' => 'nullable|date',
            'estado_actual' => 'nullable|string|max:191',  // Modificado a string
            'domain_id' => 'required|integer|exists:domains,id',
            'ciudad_id' => 'nullable|integer|exists:ciudades,id',
            'distrito_id' => 'nullable|string', 
            'imagen' => 'nullable|string',
        ]);

        // Crear un nuevo usuario asociado con el candidato
        $user = new \App\Models\User([
            'name' => $request->input('nombre') . ' ' . $request->input('apaterno') . ' ' . $request->input('amaterno'),
            'email' => $request->input('email'),
            'dni' => $request->input('identification_number'),
            'password' => Hash::make($request->input('password')),
            'domain_id' => $request->input('domain_id'),
            'rol_id' => 25,
            'type' => 'user',
            'status' => 'active',
        ]);

        // Guarda el usuario en la base de datos
        $user->save();

        // Ahora crea el registro en la tabla `av_candidatos`
        $candidato = Candidato::create([
            'code' => $request->input('code'),
            'identification_document_id' => $request->input('identification_document_id') ?: null,
            'identification_number' => $request->input('identification_number'),
            'apaterno' => $request->input('apaterno'),
            'amaterno' => $request->input('amaterno'),
            'nombre' => $request->input('nombre'),
            'phone' => $request->input('telefono'),
            'marital_status_id' => $request->input('marital_status_id'),
            'puesto' => $request->input('puesto'),
            'date_birth' => $request->input('fecha_nacimiento') ?: null,
            'age' => $request->input('age'),
            'education_degree_id' => $request->input('education_degree_id')?: null,
            'profesion' => $request->input('profesion'),  // Ahora es un string
            'ocupacion_actual' => $request->input('ocupacion_actual'),  // Ahora es un string
            'email' => $request->input('email'),
            'sex' => $request->input('genero'),
            'date_affiliation' => $request->input('fecha_afiliacion') ?: null,
            'estado_actual' => $request->input('estado_actual'),  // Ahora es un string
            'domain_id' => $request->input('domain_id'),
            'ciudad_id' => $request->input('ciudad_id'),
            'distrito_id' => $request->input('distrito_id'),
            'user_id' => $user->id,
            'image' => $request->input('imagen'),
        ]);

        // Verificar si el candidato fue creado correctamente antes de actualizar el usuario
        if ($candidato && $candidato->id) {
            $user->update(['candidato_id' => $candidato->id]);
        } else {
            return response()->json(['error' => 'No se pudo crear el candidato.'], 500);
        }

        return response()->json(['candidato' => $candidato], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Buscar el candidato por su ID
        $candidato = Candidato::with(['user','distrito.province', 'distrito.department'])->findOrFail($id);
        
        // Obtener el usuario asociado al candidato
        $user = $candidato->user;
        
        // Verificar si se encontr贸 el usuario
        if ($user) {
            // Devuelve los datos del candidato y un indicador de que hay una contrase帽a almacenada
            return response()->json([
                'candidato' => $candidato,
                'password_stored' => $user->password ? true : false,
            ], 200);
        } else {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }
    }
    
    public function getByCiudad($ciudad_id)
    {
        // Buscar candidatos solo por ciudad_id sin incluir relaciones
        $candidatos = Candidato::with(['distrito', 'distrito.province', 'distrito.department'])
            ->where('ciudad_id', $ciudad_id)
            ->get();

        if ($candidatos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron candidatos para la ciudad especificada.'], 404);
        }

        return response()->json($candidatos, 200);
    }
    public function countCandidatosByCiudad($ciudad_id)
    {
        // Contar la cantidad de candidatos en la ciudad especificada
        $cantidadCandidatos = Candidato::where('ciudad_id', $ciudad_id)->count();
    
        return response()->json([
            'ciudad_id' => $ciudad_id,
            'cantidadCandidatos' => $cantidadCandidatos,
        ]);
    }
    
    public function showByUser($id)
    {
        $candidato = Candidato::where('user_id', $id)->first();
        return response()->json(['candidato' => $candidato]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validaci贸n de los datos
        $data = $this->validate($request, [
            'identification_number' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
            'position_code' => 'nullable|string|max:100',
            'code' => 'nullable|string|max:100',
            'identification_document_id' => 'nullable|integer',
            'apaterno' => 'nullable|string|max:100',
            'amaterno' => 'nullable|string|max:100',            
            'nombre' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'marital_status_id' => 'nullable|integer',
            'puesto' => 'nullable|integer',
            'fecha_nacimiento' => 'nullable|date',
            'age' => 'nullable|integer',
            'education_degree_id' => 'nullable|integer',
            'profesion' => 'nullable|string|max:191',
            'ocupacion_actual' => 'nullable|string|max:191',
            'email' => 'nullable|string|max:100',
            'genero' => 'nullable|string|max:1',
            'fecha_afiliacion' => 'nullable|date',
            'estado_actual' => 'nullable|string|max:191',
            'domain_id' => 'required|integer|exists:domains,id',
            'ciudad_id' => 'required|integer|exists:ciudades,id',
            'imagen' => 'nullable|string',
            'distrito_id' => 'nullable|string',
        ]);
        
        $candidato = Candidato::findOrFail($id);
        
        // Obtener los datos actuales del candidato
        $candidatoData = $candidato->toArray();
        $user = $candidato->user;
        
        // Actualizar solo los campos proporcionados en el modelo `Candidato`
        $candidato->update(array_filter([
            'position_code' => $data['position_code'] ?? $candidatoData['position_code'],
            'code' => $data['code'] ?? $candidatoData['code'],
            'identification_document_id' => $data['identification_document_id'] ?? $candidatoData['identification_document_id'],
            'identification_number' => $data['identification_number'] ?? $candidatoData['identification_number'],
            'apaterno' => $data['apaterno'] ?? $candidatoData['apaterno'],
            'amaterno' => $data['amaterno'] ?? $candidatoData['amaterno'],
            'nombre' => $data['nombre'] ?? $candidatoData['nombre'],
            'phone' => $data['telefono'] ?? $candidatoData['phone'],
            'marital_status_id' => $data['marital_status_id'] ?? $candidatoData['marital_status_id'],
            'puesto' => $data['puesto'] ?? $candidatoData['puesto'],
            'date_birth' => $data['fecha_nacimiento'] ?? $candidatoData['date_birth'],
            'age' => $data['age'] ?? $candidatoData['age'],
            'education_degree_id' => $data['education_degree_id'] ?? $candidatoData['education_degree_id'],
            'profesion' => $data['profesion'] ?? $candidatoData['profesion'],
            'ocupacion_actual' => $data['ocupacion_actual'] ?? $candidatoData['ocupacion_actual'],
            'email' => $data['email'] ?? $candidatoData['email'],
            'sex' => $data['genero'] ?? $candidatoData['sex'],
            'date_affiliation' => $data['fecha_afiliacion'] ?? $candidatoData['date_affiliation'],
            'estado_actual' => $data['estado_actual'] ?? $candidatoData['estado_actual'],
            'domain_id' => $data['domain_id'] ?? $candidatoData['domain_id'],
            'ciudad_id' => $data['ciudad_id'] ?? $candidatoData['ciudad_id'],
            'image' => $data['imagen'] ?? $candidatoData['image'],
            'distrito_id' => $data['distrito_id'] ?? $candidatoData['distrito_id'],
        ]));
        
        // Actualizar el modelo `User` asociado si se proporcionan cambios
        if ($user) {
            // Actualizar la contrase帽a solo si se proporciona una nueva
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            
            // Actualizar otros campos del usuario si es necesario
            $user->email = $data['email'] ?? $user->email;
            $user->name = $data['nombre'] ?? $user->name;
            
            // Guardar los cambios en el usuario
            $user->save();
        }
    
        return response()->json(['message' => 'Candidato actualizado correctamente', 'data' => $candidato], 200);
    }
    
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $candidato = Candidato::find($id);
    
        if (!$candidato) {
            return response()->json(['message' => 'Candidato no encontrado'], 404);
        }
    
        // Buscar y eliminar el usuario asociado al candidato
        $user = \App\Models\User::find($candidato->user_id);
        if ($user) {
            $user->delete();
        }
    
        // Eliminar el candidato
        $candidato->delete();
    
        return response()->json(['message' => 'Candidato y usuario eliminados correctamente'], 204);
    }
    
}
