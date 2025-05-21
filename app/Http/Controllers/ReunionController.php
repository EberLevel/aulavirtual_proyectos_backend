<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reunion;
use App\Models\ReunionFoto;
use App\Models\Rol;
use Illuminate\Http\Request;

class ReunionController extends Controller
{
    protected $domain_id;

    public function __construct(Request $request)
    {
        // Asignar el domain_id desde los atributos de la solicitud
        $this->domain_id = $request->attributes->get('domain_id');
    }

    // Listar reuniones
    public function index()
    {
        $reuniones = Reunion::where('domain_id', $this->domain_id)->get();
        return response()->json($reuniones, 200);
    }

    // Crear un nueva reunion
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $this->validate($request, [
            'estado' => 'required|string|max:20',
            'titulo' => 'required|string|max:191',
        ]);

        // Crear un nuevo proyecto con los datos proporcionados y el domain_id
        $reunion = Reunion::create(array_merge($request->all(), ['domain_id' => $this->domain_id]));

        return response()->json([
            'message' => 'Reunión creada correctamente',
            'data' => $reunion,
        ], 201);
    }

    // Mostrar una reunion específica por ID
    public function show($id)
    {
        $reunion = Reunion::find($id);

        if (!$reunion) {
            return response()->json(['message' => 'Reunion no encontrada'], 404);
        }

        return response()->json($reunion, 200);
    }

    // Actualizar reunion
    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $this->validate($request, [
            'estado' => 'required|string|max:20',
            'titulo' => 'required|string|max:100',
            'objetivo' => 'nullable|string',  // Campo opcional
            'resultado' => 'nullable|string', // Campo opcional
        ]);

        $reunion = Reunion::find($id);

        if (!$reunion) {
            return response()->json(['message' => 'Reunion no encontrada'], 404);
        }

        // Actualizar la reunion con los nuevos datos
        $reunion->update($request->all());

        return response()->json([
            'message' => 'Reunion actualizada correctamente',
            'data' => $reunion,
        ], 200);
    }

    public function storeFoto(Request $request, $id)
    {
        // Validar la entrada
        $this->validate($request, [
            'foto' => 'required|string', // La foto debe ser una cadena base64
        ]);

        // Buscar la reunión por su ID
        $reunion = Reunion::find($id);

        // Verificar si la reunión existe
        if (!$reunion) {
            return response()->json(['message' => 'Reunión no encontrada'], 404);
        }

        // Crear una nueva instancia de ReunionFoto
        $reunionFoto = new ReunionFoto();
        $reunionFoto->reunion_id = $reunion->id;
        $reunionFoto->foto = $request->input('foto'); // Aquí recibimos la cadena base64 de la foto

        // Guardar la foto en la base de datos
        $reunionFoto->save();

        // Retornar una respuesta exitosa
        return response()->json([
            'message' => 'Foto guardada exitosamente',
            'foto' => $reunionFoto,
        ], 201);
    }

}
