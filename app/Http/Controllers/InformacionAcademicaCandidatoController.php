<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EstadoAvance;
use Illuminate\Http\Request;
use App\Models\InformacionAcademicaCandidato;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class InformacionAcademicaCandidatoController extends Controller
{
    /**
     * Obtener los datos necesarios para el formulario de creación.
     */
    public function getDataCreate($domain_id)
    {
        try {
            $estadoAvances = EstadoAvance::where('domain_id', $domain_id)->get();
    
            return response()->json([
                'estadoAvances' => $estadoAvances
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener datos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Almacenar una nueva información académica en la base de datos.
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'estado_id' => 'required|integer|exists:estado_avance,id',
                'domain_id' => 'required|integer|exists:domains,id',
                'candidato_id' => 'required|integer|exists:av_candidatos,id',
                'nombre' => 'required|string|max:255',
                'avance' => 'required|integer',
                'observaciones' => 'nullable|string',
                'certificado' => 'nullable|string'
            ]);

            $informacionAcademica = new InformacionAcademicaCandidato();
            $informacionAcademica->estado_id = $request->estado_id;
            $informacionAcademica->domain_id = $request->domain_id;
            $informacionAcademica->candidato_id = $request->candidato_id;
            $informacionAcademica->nombre = $request->nombre;
            $informacionAcademica->avance = $request->avance;
            $informacionAcademica->observaciones = $request->observaciones;
            $informacionAcademica->certificado = $request->certificado;
            $informacionAcademica->save();

            return response()->json(['message' => 'Información académica creada correctamente', 'data' => $informacionAcademica], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Error al guardar la información académica: ' . $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al guardar la información académica: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener todas las informaciones académicas por dominio.
     */
    public function getByDomainId($domain_id)
    {
        try {
            $informacionAcademica = InformacionAcademicaCandidato::where('domain_id', $domain_id)->get();

            if ($informacionAcademica->isEmpty()) {
                return response()->json(['message' => 'No se encontraron registros para este domain_id'], 404);
            }

            return response()->json(['data' => $informacionAcademica], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener la información académica: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una información académica específica por candidato.
     */
    public function show($id)
    {
        try {
            $informacionAcademica = InformacionAcademicaCandidato::where('candidato_id', $id)->get();

            if ($informacionAcademica->isEmpty()) {
                return response()->json(['message' => 'No se encontraron registros para este candidato'], 404);
            }

            return response()->json(['data' => $informacionAcademica], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener la información académica: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar la información académica existente.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'estado_id' => 'required|integer|exists:estado_avance,id',
                'domain_id' => 'required|integer|exists:domains,id',
                'candidato_id' => 'required|integer|exists:av_candidatos,id',
                'nombre' => 'required|string|max:255',
                'avance' => 'required|integer',
                'observaciones' => 'nullable|string',
                'certificado' => 'nullable|string'
            ]);

            $informacionAcademica = InformacionAcademicaCandidato::findOrFail($id);

            $informacionAcademica->estado_id = $request->estado_id;
            $informacionAcademica->domain_id = $request->domain_id;
            $informacionAcademica->candidato_id = $request->candidato_id;
            $informacionAcademica->nombre = $request->nombre;
            $informacionAcademica->avance = $request->avance;
            $informacionAcademica->observaciones = $request->observaciones;
            $informacionAcademica->certificado = $request->certificado;
            $informacionAcademica->save();

            return response()->json(['message' => 'Información académica actualizada correctamente', 'data' => $informacionAcademica], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Error al actualizar la información académica: ' . $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar la información académica: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar la información académica.
     */
    public function destroy($id)
    {
        try {
            $informacionAcademica = InformacionAcademicaCandidato::findOrFail($id);
            $informacionAcademica->delete();
            return response()->json(['message' => 'Información académica eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Error al eliminar la información académica: ' . $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar la información académica: ' . $e->getMessage()], 500);
        }
    }
}
