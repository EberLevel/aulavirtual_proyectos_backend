<?php

namespace App\Http\Controllers;

use App\Models\Ano;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AnoController extends Controller
{
    /**
     * Obtener los años por domain_id
     *
     * @param  int  $domain_id
     * @return \Illuminate\Http\Response
     */
    public function index($domain_id)
    {
        try {
            $anos = Ano::where('domain_id', $domain_id)
                ->orderBy('nombre', 'desc') // Ordenar por año descendente
                ->paginate(10);

            return response()->json($anos, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching anos for domain ' . $domain_id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los años: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crear un nuevo año
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'domain_id' => 'required|numeric|exists:domains,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Verificar si ya existe un año con el mismo nombre en el dominio
            $existingAno = Ano::where('nombre', $request->nombre)
                ->where('domain_id', $request->domain_id)
                ->first();

            if ($existingAno) {
                return response()->json(['message' => 'Ya existe un año con ese nombre en este dominio'], 400);
            }

            $ano = Ano::create($request->all());

            return response()->json([
                'message' => 'Año creado correctamente',
                'data' => $ano
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating ano: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear el año: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener un año por su ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $ano = Ano::findOrFail($id);
            return response()->json(['data' => $ano], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Año no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching ano ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener el año: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un año por su ID
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'domain_id' => 'required|numeric|exists:domains,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $ano = Ano::findOrFail($id);

            // Verificar si ya existe otro año con el mismo nombre en el dominio
            $existingAno = Ano::where('nombre', $request->nombre)
                ->where('domain_id', $request->domain_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existingAno) {
                return response()->json(['message' => 'Ya existe otro año con ese nombre en este dominio'], 400);
            }

            $ano->update($request->all());

            return response()->json([
                'message' => 'Año actualizado correctamente',
                'data' => $ano
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Año no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating ano ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar el año: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un año por su ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ano = Ano::findOrFail($id);

            // Verificar si el año está siendo usado por algún CvBank
            $cvBanksCount = \App\Models\CvBank::where('ano_id', $id)->count();
            
            if ($cvBanksCount > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el año porque está siendo usado por ' . $cvBanksCount . ' postulante(s)'
                ], 400);
            }

            $ano->delete();

            return response()->json(['message' => 'Año eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Año no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting ano ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el año: ' . $e->getMessage()], 500);
        }
    }
}