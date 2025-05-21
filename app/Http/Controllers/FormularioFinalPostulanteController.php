<?php

namespace App\Http\Controllers;

use App\Models\FormularioFinalPostulante;
use App\Models\OcupacionActual;
use App\Models\Escala;
use App\Models\NivelCargo;
use Illuminate\Http\Request;
use Exception;

class FormularioFinalPostulanteController extends Controller
{
    public function index()
    {
        try {
            $formularios = FormularioFinalPostulante::with([
                'estadoActual', 
                'aceptacion', 
                'nivelCargoFinal', 
                'puntaje', 
                'postulante'
            ])->get();

            return response()->json(['data' => $formularios], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener los formularios: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'observaciones' => 'nullable|string',
            'estado_actual_id' => 'required|integer|exists:ocupacion_actual,id',
            'aceptacion_id' => 'required|integer|exists:escala,id',
            'nivel_cargo_final_id' => 'required|integer|exists:nivel_puesto,id',
            'puntaje_id' => 'required|integer|exists:escala,id',
            'institucion' => 'nullable|string|max:255',
            'tabla_referencia' => 'required|string|max:255',
            'postulante_id' => 'required|integer|exists:cv_banks,id',
        ]);

        try {
            $formulario = FormularioFinalPostulante::create($request->all());

            return response()->json(['message' => 'Formulario final creado correctamente', 'data' => $formulario], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al crear el formulario: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $formulario = FormularioFinalPostulante::with([
                'estadoActual', 
                'aceptacion', 
                'nivelCargoFinal', 
                'puntaje', 
                'postulante'
            ])->findOrFail($id);

            return response()->json(['data' => $formulario], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el formulario: ' . $e->getMessage()], 500);
        }
    }
    public function getDataCreate()
    {
        try {
            $estados = OcupacionActual::all();
            $aceptaciones = Escala::all();
            $nivelesCargo = NivelCargo::all();
            $puntajes = Escala::all(); // Si la escala de puntaje es la misma que la de aceptación.

            return response()->json([
                'estados' => $estados,
                'aceptaciones' => $aceptaciones,
                'nivelesCargo' => $nivelesCargo,
                'puntajes' => $puntajes,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener los datos para el formulario: ' . $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'observaciones' => 'nullable|string',
            'estado_actual_id' => 'required|integer|exists:ocupacion_actual,id',
            'aceptacion_id' => 'required|integer|exists:escala,id',
            'nivel_cargo_final_id' => 'required|integer|exists:nivel_puesto,id',
            'puntaje_id' => 'required|integer|exists:escala,id',
            'institucion' => 'nullable|string|max:255',
            'tabla_referencia' => 'required|string|max:255',
            'postulante_id' => 'required|integer|exists:cv_banks,id',
        ]);

        try {
            $formulario = FormularioFinalPostulante::findOrFail($id);
            $formulario->update($request->all());

            return response()->json(['message' => 'Formulario final actualizado correctamente', 'data' => $formulario], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar el formulario: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $formulario = FormularioFinalPostulante::findOrFail($id);
            $formulario->delete();

            return response()->json(['message' => 'Formulario final eliminado correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar el formulario: ' . $e->getMessage()], 500);
        }
    }
}
