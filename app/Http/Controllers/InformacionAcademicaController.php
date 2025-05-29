<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GradoInstruccion;
use App\Models\Profesion;
use App\Models\EstadoAvance;
use App\Models\InformacionAcademica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Storage;

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
            // Transform 'validado' input to boolean (if provided)
            $input = $request->all();
            if (isset($input['validado'])) {
                $input['validado'] = filter_var($input['validado'], FILTER_VALIDATE_BOOLEAN);
            }

            // Define validation rules
            $rules = [
                'grado_instruccion_id' => 'required|exists:grado_instruccion,id',
                'profesion_id' => 'required|exists:profesion,id',
                'estado_avance_id' => 'required|exists:estado_avance,id',
                'domain_id' => 'required|exists:domains,id',
                'id_postulante' => 'required|exists:cv_banks,id',
                'institucion' => 'required|string|max:255',
                'fecha_inicio' => 'required|date',
                'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
                'observaciones' => 'nullable|string|max:500',
                'nro_pagina_cv' => 'nullable|integer',
                'validado' => 'nullable|boolean',
                'imagen_certificado' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];

            // Create validator instance
            $validator = Validator::make($input, $rules);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get validated data
            $validated = $validator->validated();

            // Handle file upload
            $path = null;
            if ($request->hasFile('imagen_certificado') && $request->file('imagen_certificado')->isValid()) {
                $path = $request->file('imagen_certificado')->store('certificados', 'public');
            }

            // Create new record
            $informacionAcademica = InformacionAcademica::create([
                'grado_instruccion_id' => $validated['grado_instruccion_id'],
                'profesion_id' => $validated['profesion_id'],
                'estado_avance_id' => $validated['estado_avance_id'],
                'domain_id' => $validated['domain_id'],
                'id_postulante' => $validated['id_postulante'],
                'institucion' => $validated['institucion'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_termino' => $validated['fecha_termino'],
                'observaciones' => $validated['observaciones'] ?? null,
                'nro_pagina_cv' => $validated['nro_pagina_cv'] ?? null,
                'validado' => $validated['validado'] ?? false,
                'imagen_certificado' => $path,
            ]);

            return response()->json([
                'message' => 'Información académica creada correctamente',
                'data' => $informacionAcademica
            ], 201);
        } catch (Exception $e) {
            \Log::error('Error in store method: ' . $e->getMessage());
            return response()->json(['error' => 'Error al guardar la información académica: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar el campo validado de una información académica.
     */
    public function updateValidado(Request $request, $id)
    {
        try {
            // Define validation rules
            $rules = [
                'validado' => 'required|in:0,1',
            ];

            // Create validator instance
            $validator = Validator::make($request->all(), $rules);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find the existing record
            $informacionAcademica = InformacionAcademica::findOrFail($id);

            // Update only the validado field
            $informacionAcademica->update([
                'validado' => (bool) $request->validado, // Convert 1/0 to boolean for storage
            ]);

            return response()->json([
                'message' => 'Estado de validación actualizado correctamente',
                'data' => $informacionAcademica
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        } catch (Exception $e) {
            \Log::error('Error in updateValidado method: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el estado de validación: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar la información académica existente.
     */
    public function update(Request $request, $id)
    {
        try {
            // Transform 'validado' input to boolean (if provided)
            $input = $request->all();
            if (isset($input['validado'])) {
                $input['validado'] = filter_var($input['validado'], FILTER_VALIDATE_BOOLEAN);
            }

            // Define validation rules
            $rules = [
                'grado_instruccion_id' => 'required|exists:grado_instruccion,id',
                'profesion_id' => 'required|exists:profesion,id',
                'estado_avance_id' => 'required|exists:estado_avance,id',
                'domain_id' => 'required|exists:domains,id',
                'id_postulante' => 'required|exists:cv_banks,id',
                'institucion' => 'required|string|max:255',
                'fecha_inicio' => 'required|date',
                'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
                'observaciones' => 'nullable|string|max:500',
                'nro_pagina_cv' => 'nullable|integer',
                'validado' => 'nullable|boolean',
                'imagen_certificado' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];

            // Create validator instance
            $validator = Validator::make($input, $rules);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get validated data
            $validated = $validator->validated();

            // Find the existing record
            $informacionAcademica = InformacionAcademica::findOrFail($id);

            // Handle file upload
            if ($request->hasFile('imagen_certificado') && $request->file('imagen_certificado')->isValid()) {
                // Delete old file if it exists
                if ($informacionAcademica->imagen_certificado) {
                    Storage::disk('public')->delete($informacionAcademica->imagen_certificado);
                }
                $path = $request->file('imagen_certificado')->store('certificados', 'public');
                $informacionAcademica->imagen_certificado = $path;
            }

            // Update record
            $informacionAcademica->update([
                'grado_instruccion_id' => $validated['grado_instruccion_id'],
                'profesion_id' => $validated['profesion_id'],
                'estado_avance_id' => $validated['estado_avance_id'],
                'domain_id' => $validated['domain_id'],
                'id_postulante' => $validated['id_postulante'],
                'institucion' => $validated['institucion'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_termino' => $validated['fecha_termino'],
                'observaciones' => $validated['observaciones'] ?? null,
                'nro_pagina_cv' => $validated['nro_pagina_cv'] ?? null,
                'validado' => $validated['validado'] ?? $informacionAcademica->validado,
            ]);

            return response()->json([
                'message' => 'Información académica actualizada correctamente',
                'data' => $informacionAcademica
            ], 200);
        } catch (Exception $e) {
            \Log::error('Error in update method: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la información académica: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener información académica por domain_id.
     */
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
     * Mostrar información académica por id_postulante.
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
     * Eliminar la información académica.
     */
    public function destroy($id)
    {
        try {
            $informacionAcademica = InformacionAcademica::findOrFail($id);

            // Eliminar la imagen asociada si existe
            if ($informacionAcademica->imagen_certificado) {
                Storage::disk('public')->delete($informacionAcademica->imagen_certificado);
            }

            $informacionAcademica->delete();
            return response()->json(['message' => 'Información académica eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Error al eliminar la información académica: ' . $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar la información académica: ' . $e->getMessage()], 500);
        }
    }
}