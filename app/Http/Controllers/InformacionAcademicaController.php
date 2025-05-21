<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GradoInstruccion;
use App\Models\Profesion;
use App\Models\EstadoAvance;
use Illuminate\Http\Request;
use App\Models\InformacionAcademica;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class InformacionAcademicaController extends Controller
{
    /**
     * Obtener los datos necesarios para el formulario de creación.
     */
    public function getDataCreate($domain_id)
    {
        try {
            $gradosInstruccion = GradoInstruccion::where('domain_id', $domain_id)->get();
            $profesiones = Profesion::where('domain_id', $domain_id)->get();
            $estadoAvances = EstadoAvance::where('domain_id', $domain_id)->get();
    
            return response()->json([
                'gradosInstruccion' => $gradosInstruccion,
                'profesiones' => $profesiones,
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
                'grado_instruccion_id' => 'required|integer|exists:grado_instruccion,id',
                'profesion_id' => 'required|integer|exists:profesion,id',
                'estado_avance_id' => 'required|integer|exists:estado_avance,id',
                'domain_id' => 'required|integer|exists:domains,id',
                'id_postulante' => 'required|integer|exists:cv_banks,id',
                'institucion' => 'required|string|max:200',
                'fecha_inicio' => 'required|date',
                'fecha_termino' => 'required|date',
                'observaciones' => 'nullable|string',
                'imagen_certificado' => 'nullable|string'
            ]);

            $informacionAcademica = new InformacionAcademica();
            $informacionAcademica->grado_instruccion_id = $request->grado_instruccion_id;
            $informacionAcademica->profesion_id = $request->profesion_id;
            $informacionAcademica->estado_avance_id = $request->estado_avance_id;
            $informacionAcademica->domain_id = $request->domain_id;
            $informacionAcademica->id_postulante = $request->id_postulante;
            $informacionAcademica->institucion = $request->institucion;
            $informacionAcademica->fecha_inicio = $request->fecha_inicio;
            $informacionAcademica->fecha_termino = $request->fecha_termino;
            $informacionAcademica->observaciones = $request->observaciones;
            $informacionAcademica->imagen_certificado = $request->imagen_certificado; // Guardar la imagen en base64
            $informacionAcademica->save();

            return response()->json(['message' => 'Información académica creada correctamente', 'data' => $informacionAcademica], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Error al guardar la información académica: ' . $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al guardar la información académica: ' . $e->getMessage()], 500);
        }
    }


    public function getByDomainId($domain_id)
    {
        try {
            $informacionAcademica = InformacionAcademica::where('domain_id', $domain_id)->get();

            if ($informacionAcademica->isEmpty()) {
                return response()->json(['message' => 'No se encontraron registros para este domain_id'], 404);
            }

            return response()->json(['data' => $informacionAcademica], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener la información académica: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Obtener todas las informaciones académicas, con opción de filtrar por id_postulante.
     */
    public function index(Request $request)
    {
        try {
            $query = InformacionAcademica::query();

            if ($request->has('id_postulante')) {
                $query->where('id_postulante', $request->id_postulante);
            }

            $informacionAcademica = $query->get();
            return response()->json(['data' => $informacionAcademica], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener la información académica: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Mostrar una información académica específica por id_postulante.
     */
    public function show($id)
    {
        try {
            $informacionAcademica = InformacionAcademica::where('id_postulante', $id)->get();

            if ($informacionAcademica->isEmpty()) {
                return response()->json(['message' => 'No se encontraron registros para este postulante'], 404);
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
                'grado_instruccion_id' => 'required|integer|exists:grado_instruccion,id',
                'profesion_id' => 'required|integer|exists:profesion,id',
                'estado_avance_id' => 'required|integer|exists:estado_avance,id',
                'domain_id' => 'required|integer|exists:domains,id',
                'id_postulante' => 'required|integer|exists:cv_banks,id',
                'institucion' => 'required|string|max:200',
                'fecha_inicio' => 'required|date',
                'fecha_termino' => 'required|date',
                'observaciones' => 'nullable|string',
                'imagen_certificado' => 'nullable|string'
            ]);

            $informacionAcademica = InformacionAcademica::findOrFail($id);

            $informacionAcademica->grado_instruccion_id = $request->grado_instruccion_id;
            $informacionAcademica->profesion_id = $request->profesion_id;
            $informacionAcademica->estado_avance_id = $request->estado_avance_id;
            $informacionAcademica->domain_id = $request->domain_id;
            $informacionAcademica->id_postulante = $request->id_postulante;
            $informacionAcademica->institucion = $request->institucion;
            $informacionAcademica->fecha_inicio = $request->fecha_inicio;
            $informacionAcademica->fecha_termino = $request->fecha_termino;
            $informacionAcademica->observaciones = $request->observaciones;
            $informacionAcademica->imagen_certificado = $request->imagen_certificado; // Guardar la imagen en base64
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
            $informacionAcademica = InformacionAcademica::findOrFail($id);
            $informacionAcademica->delete();
            return response()->json(['message' => 'Información académica eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Error al eliminar la información académica: ' . $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar la información académica: ' . $e->getMessage()], 500);
        }
    }
}
