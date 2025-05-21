<?php

namespace App\Http\Controllers;

use App\Models\ReferenciaFamiliar;
use Illuminate\Http\Request;
use Exception;

class ReferenciasFamiliaresController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'ocupacion' => 'required|string|max:255',
            'id_postulante' => 'required|integer|exists:cv_banks,id',
            'domain_id' => 'required|integer|exists:domains,id',
        ]);

        try {
            $referenciaFamiliar = new ReferenciaFamiliar($request->all());
            $referenciaFamiliar->save();

            return response()->json(['message' => 'Referencia familiar creada correctamente', 'data' => $referenciaFamiliar], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al crear la referencia familiar: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'ocupacion' => 'required|string|max:255',
            'id_postulante' => 'required|integer|exists:cv_banks,id',
            'domain_id' => 'required|integer|exists:domains,id',
        ]);

        try {
            $referenciaFamiliar = ReferenciaFamiliar::findOrFail($id);
            $referenciaFamiliar->update($request->all());

            return response()->json(['message' => 'Referencia familiar actualizada correctamente', 'data' => $referenciaFamiliar], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar la referencia familiar: ' . $e->getMessage()], 500);
        }
    }

    public function index($id_postulante)
    {
        try {
            $referenciasFamiliares = ReferenciaFamiliar::where('id_postulante', $id_postulante)->get();
            return response()->json(['data' => $referenciasFamiliares], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener las referencias familiares: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $referenciaFamiliar = ReferenciaFamiliar::findOrFail($id);
            $referenciaFamiliar->delete();
            return response()->json(['message' => 'Referencia familiar eliminada correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar la referencia familiar: ' . $e->getMessage()], 500);
        }
    }
}
