<?php

namespace App\Http\Controllers;

use App\Models\ExperienciaLaboral;
use Illuminate\Http\Request;
use App\Models\VinculoLaboral;
use App\Models\ModalidadPuesto;
use Exception;

class ExperienciaLaboralController extends Controller
{
    public function getDataCreate($domain_id)
    {
        try {
            $vinculos = VinculoLaboral::where('domain_id', $domain_id)->get();
            $modalidades = ModalidadPuesto::where('domain_id', $domain_id)->get();
    
            return response()->json([
                'vinculos' => $vinculos,
                'modalidades' => $modalidades
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener datos: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tipo_institucion' => 'required|string|max:255',
            'puesto' => 'required|string|max:255',
            'institucion' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'remuneracion_mensual' => 'required|numeric',
            'fecha_ingreso' => 'required|date',
            'fecha_termino' => 'required|date',
            'tiempo_experiencia_especifica' => 'nullable|string|max:255',
            'tiempo_experiencia_general' => 'required|string|max:255',
            'dias_cuenta_regresiva' => 'required|integer',
            'funciones' => 'required|string',
            'motivo_termino' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'imagen' => 'nullable|string',
            'vinculo_laboral_id' => 'nullable|integer|exists:vinculo_laboral,id',
            'modalidad_puesto_id' => 'required|integer|exists:modalidad_puesto,id',
            'domain_id' => 'required|integer|exists:domains,id',
            'id_postulante' => 'required|integer|exists:cv_banks,id',
        ]);

        try {
            $experiencia = new ExperienciaLaboral($request->all());
            $experiencia->save();

            return response()->json(['message' => 'Experiencia laboral creada correctamente', 'data' => $experiencia], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al crear la experiencia laboral: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tipo_institucion' => 'required|string|max:255',
            'puesto' => 'required|string|max:255',
            'institucion' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'remuneracion_mensual' => 'required|numeric',
            'fecha_ingreso' => 'required|date',
            'fecha_termino' => 'required|date',
            'tiempo_experiencia_especifica' => 'nullable|string|max:255',
            'tiempo_experiencia_general' => 'required|string|max:255',
            'dias_cuenta_regresiva' => 'required|integer',
            'funciones' => 'required|string',
            'motivo_termino' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'imagen' => 'nullable|string',
            'vinculo_laboral_id' => 'nullable|integer|exists:vinculo_laboral,id',
            'modalidad_puesto_id' => 'required|integer|exists:modalidad_puesto,id',
            'domain_id' => 'required|integer|exists:domains,id',
            'id_postulante' => 'required|integer|exists:cv_banks,id',
        ]);

        try {
            $experiencia = ExperienciaLaboral::findOrFail($id);
            $experiencia->update($request->all());

            return response()->json(['message' => 'Experiencia laboral actualizada correctamente', 'data' => $experiencia], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar la experiencia laboral: ' . $e->getMessage()], 500);
        }
    }

    public function index($id_postulante)
    {
        try {
            $experiencias = ExperienciaLaboral::with(['vinculoLaboral', 'modalidadPuesto'])
                ->where('id_postulante', $id_postulante)
                ->get();
            return response()->json(['data' => $experiencias], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener las experiencias laborales: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $experiencia = ExperienciaLaboral::findOrFail($id);
            $experiencia->delete();
            return response()->json(['message' => 'Experiencia laboral eliminada correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar la experiencia laboral: ' . $e->getMessage()], 500);
        }
    }
}
