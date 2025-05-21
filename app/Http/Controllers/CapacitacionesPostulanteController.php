<?php

namespace App\Http\Controllers;

use App\Models\CapacitacionPostulante;
use Illuminate\Http\Request;
use App\Models\Ano;
use Exception;
use App\Models\Capacitacion;
class CapacitacionesPostulanteController extends Controller
{
    // Crear una nueva capacitación
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'institucion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date',
            'observaciones' => 'nullable|string',
            'imagen_certificado' => 'nullable|string',
            'domain_id' => 'required|integer|exists:domains,id',
            'id_postulante' => 'required|integer|exists:cv_banks,id',
        ]);

        try {
            // Calcular el tiempo entre las fechas de inicio y término
            $fechaInicio = new \DateTime($request->fecha_inicio);
            $fechaTermino = new \DateTime($request->fecha_termino);
            $interval = $fechaInicio->diff($fechaTermino);

            // Formatear solo años y meses
            $tiempo = '';
            if ($interval->y > 0) {
                $tiempo .= $interval->y . ' años';
            }
            if ($interval->m > 0) {
                $tiempo .= ($interval->y > 0 ? ', ' : '') . $interval->m . ' meses';
            }

            $capacitacion = new CapacitacionPostulante($request->all());
            $capacitacion->tiempo = $tiempo; // Asignar el tiempo calculado
            $capacitacion->save();

            return response()->json(['message' => 'Capacitación creada correctamente', 'data' => $capacitacion], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al crear la capacitación: ' . $e->getMessage()], 500);
        }
    }

    // Actualizar una capacitación existente
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'institucion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date',
            'observaciones' => 'nullable|string',
            'imagen_certificado' => 'nullable|string',
            'domain_id' => 'required|integer|exists:domains,id',
            'id_postulante' => 'required|integer|exists:cv_banks,id'
        ]);

        try {
            $capacitacion = CapacitacionPostulante::findOrFail($id);

            // Calcular el tiempo entre las fechas de inicio y término
            $fechaInicio = new \DateTime($request->fecha_inicio);
            $fechaTermino = new \DateTime($request->fecha_termino);
            $interval = $fechaInicio->diff($fechaTermino);

            // Formatear solo años y meses
            $tiempo = '';
            if ($interval->y > 0) {
                $tiempo .= $interval->y . ' años';
            }
            if ($interval->m > 0) {
                $tiempo .= ($interval->y > 0 ? ', ' : '') . $interval->m . ' meses';
            }

            $capacitacion->update($request->all());
            $capacitacion->tiempo = $tiempo; // Asignar el tiempo calculado
            $capacitacion->save();

            return response()->json(['message' => 'Capacitación actualizada correctamente', 'data' => $capacitacion], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar la capacitación: ' . $e->getMessage()], 500);
        }
    }

    // Obtener todas las capacitaciones por postulante
    public function index($id_postulante)
    {
        try {
            $capacitaciones = CapacitacionPostulante::where('id_postulante', $id_postulante)->get();
            return response()->json(['data' => $capacitaciones], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener las capacitaciones: ' . $e->getMessage()], 500);
        }
    }

    // Eliminar una capacitación
    public function destroy($id)
    {
        try {
            $capacitacion = CapacitacionPostulante::findOrFail($id);
            $capacitacion->delete();
            return response()->json(['message' => 'Capacitación eliminada correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar la capacitación: ' . $e->getMessage()], 500);
        }
    }
    public function getDataCreate($domain_id)
    {
        try {
            $capacitaciones = CapacitacionPostulante::where('domain_id', $domain_id)->get();
            return response()->json(['estados' => $capacitaciones], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener datos: ' . $e->getMessage()], 500);
        }
    }
}
