<?php

namespace App\Http\Controllers;

use App\Models\Profesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProfesionController extends Controller
{
    
    public function index($domain_id)  // Recibe el parámetro domain_id
    {
        // Obtener las profesiones filtradas por el domain_id
        $profesiones = Profesion::where('domain_id', $domain_id)->paginate(10);

        return response()->json($profesiones, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos de entrada usando Validator
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:191',
            'domain_id' => 'required|integer|exists:domains,id',
        ]);

        // Si hay errores de validación, retornar con un código 400
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Crear una nueva profesión con los datos proporcionados
        $profesion = Profesion::create($request->all());

        return response()->json([
            'message' => 'Profesión creada correctamente',
            'data' => $profesion,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Buscar la profesión por ID
        $profesion = Profesion::find($id);

        // Si no se encuentra la profesión, retornar error 404
        if (!$profesion) {
            return response()->json(['message' => 'Profesión no encontrada'], 404);
        }

        return response()->json(['data' => $profesion], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validación de los datos de entrada usando Validator
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:191',
            'domain_id' => 'required|integer|exists:domains,id',
        ]);

        // Si hay errores de validación, retornar con un código 400
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Buscar la profesión por ID
        $profesion = Profesion::find($id);

        // Si no se encuentra la profesión, retornar error 404
        if (!$profesion) {
            return response()->json(['message' => 'Profesión no encontrada'], 404);
        }

        // Actualizar la profesión con los nuevos datos
        $profesion->update($request->all());

        return response()->json([
            'message' => 'Profesión actualizada correctamente',
            'data' => $profesion,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Buscar la profesión por ID
        $profesion = Profesion::find($id);

        // Si no se encuentra la profesión, retornar error 404
        if (!$profesion) {
            return response()->json(['message' => 'Profesión no encontrada'], 404);
        }

        // Eliminar la profesión
        $profesion->delete();

        return response()->json(['message' => 'Profesión eliminada correctamente'], 204);
    }

    public function massiveStore(Request $request)
    {
        // Validar el payload principal
        $validator = Validator::make($request->all(), [
            'domain_id' => 'required|integer|exists:domains,id',
            'profesiones' => 'required|array|min:1',
            'profesiones.*.nombre' => 'required|string|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 400);
        }

        $domain_id = $request->input('domain_id');
        $profesiones = $request->input('profesiones');
        $success_count = 0;
        $errors = [];

        // Iniciar una transacción para garantizar consistencia
        DB::beginTransaction();
        try {
            foreach ($profesiones as $index => $profesionData) {
                // Validar cada profesión individualmente
                $validator = Validator::make($profesionData, [
                    'nombre' => 'required|string|max:191|unique:profesion,nombre,NULL,id,domain_id,' . $domain_id,
                ]);

                if ($validator->fails()) {
                    $errors[] = [
                        'row' => $index + 2, // +2 porque la fila 1 es el encabezado
                        'errors' => $validator->errors()->all()
                    ];
                    continue;
                }

                // Crear la profesión
                Profesion::create([
                    'nombre' => $profesionData['nombre'],
                    'domain_id' => $domain_id,
                ]);
                $success_count++;
            }

            if (count($errors) > 0) {
                // Si hay errores, hacer rollback parcial
                DB::rollBack();
                return response()->json([
                    'message' => 'Algunas profesiones no se pudieron crear',
                    'success_count' => $success_count,
                    'errors' => $errors,
                ], 207); // 207 Multi-Status para indicar éxito parcial
            }

            // Confirmar la transacción si todo está bien
            DB::commit();
            return response()->json([
                'message' => 'Profesiones creadas correctamente',
                'success_count' => $success_count,
                'errors' => [],
            ], 201);
        } catch (\Exception $e) {
            // En caso de error inesperado, hacer rollback
            DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar la carga masiva',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
