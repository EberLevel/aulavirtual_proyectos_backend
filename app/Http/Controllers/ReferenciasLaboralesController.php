<?php
namespace App\Http\Controllers;

use App\Models\ReferenciaLaboral;
use Illuminate\Http\Request;
use Exception;

class ReferenciasLaboralesController extends Controller
{
    // Obtener referencias laborales por postulante
    public function index($id_postulante)
    {
        try {
            $referencias = ReferenciaLaboral::where('id_postulante', $id_postulante)->get();
            return response()->json(['data' => $referencias], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener las referencias laborales: ' . $e->getMessage()], 500);
        }
    }

    // Crear una nueva referencia laboral
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'celular' => 'required|string|max:15',
            'ocupacion' => 'required|string|max:255',
            'id_postulante' => 'required|integer|exists:cv_banks,id',
            'domain_id' => 'required|integer|exists:domains,id',
        ]);

        try {
            $referencia = new ReferenciaLaboral($request->all());
            $referencia->save();

            return response()->json(['message' => 'Referencia laboral creada correctamente', 'data' => $referencia], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al crear la referencia laboral: ' . $e->getMessage()], 500);
        }
    }

    // Actualizar una referencia laboral existente
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'celular' => 'required|string|max:15',
            'ocupacion' => 'required|string|max:255',
            'id_postulante' => 'required|integer|exists:cv_banks,id',
            'domain_id' => 'required|integer|exists:domains,id',
        ]);

        try {
            $referencia = ReferenciaLaboral::findOrFail($id);
            $referencia->update($request->all());

            return response()->json(['message' => 'Referencia laboral actualizada correctamente', 'data' => $referencia], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar la referencia laboral: ' . $e->getMessage()], 500);
        }
    }

    // Eliminar una referencia laboral
    public function destroy($id)
    {
        try {
            $referencia = ReferenciaLaboral::findOrFail($id);
            $referencia->delete();
            return response()->json(['message' => 'Referencia laboral eliminada correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar la referencia laboral: ' . $e->getMessage()], 500);
        }
    }
}
