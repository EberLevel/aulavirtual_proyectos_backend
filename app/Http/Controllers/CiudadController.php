<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    /**
     * Mostrar una lista de las ciudades.
     */
    public function index(Request $request)
    {
        $query = Ciudad::query();

        // Opcional: Filtrar por código, estado o nombre
        if ($request->has('codigo')) {
            $query->where('codigo', 'LIKE', '%' . $request->codigo . '%');
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('nombre')) {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }

        $ciudades = $query->paginate(10);

        return response()->json($ciudades, 200);
    }


    /**
     * Almacenar una nueva ciudad.
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'codigo' => 'required|string|max:50|unique:ciudades,codigo',
                'nombre' => 'required|string|max:191',
                'estado' => 'required|in:aprobado,desaprobado,observado,en_evaluacion',
                'observaciones' => 'nullable|string',
                'domain_id' => 'required|integer|exists:domains,id',
                'avance' => 'required|integer|min:0|max:100',
            ]);

            $ciudad = Ciudad::create([
                'codigo' => $request->input('codigo'),
                'nombre' => $request->input('nombre'),
                'estado' => $request->input('estado'),
                'observaciones' => $request->input('observaciones'),
                'domain_id' => $request->input('domain_id'),
                'avance' => $request->input('avance')
            ]);

            return response()->json(['ciudad' => $ciudad], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * obtenerCiudadesPorEstado no aplicada todavia
     */
    public function obtenerCiudadesPorEstado($estado)
    {
        $ciudades = Ciudad::where('estado', $estado)->get();
        return response()->json($ciudades, 200);
    }

    /**
     * Mostrar una ciudad específica.
     */
    public function show($id)
    {
        $ciudad = Ciudad::findOrFail($id);
        return response()->json(['ciudad' => $ciudad], 200);
    }

    /**
     * Actualizar una ciudad específica.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:50|unique:ciudades,codigo,' . $id,
            'nombre' => 'required|string|max:191',
            'estado' => 'required|in:aprobado,desaprobado,observado,en_evaluacion',
            'observaciones' => 'nullable|string',
            'domain_id' => 'required|integer|exists:domains,id',
            'avance' => 'required|integer|min:0|max:100',
        ]);

        $ciudad = Ciudad::findOrFail($id);
        $ciudad->update([
            'codigo' => $request->input('codigo'),
            'nombre' => $request->input('nombre'),
            'estado' => $request->input('estado'),
            'observaciones' => $request->input('observaciones'),
            'domain_id' => $request->input('domain_id'),
            'avance' => $request->input('avance'),
        ]);

        return response()->json(['message' => 'Ciudad actualizada correctamente', 'ciudad' => $ciudad], 200);
    }

    public function listByDomain($domain_id)
    {
        // Filtrar las ciudades por domain_id
        $ciudades = Ciudad::where('domain_id', $domain_id)->get();

        return response()->json($ciudades, 200);
    }

    /**
     * Eliminar una ciudad específica.
     */
    public function destroy($id)
    {
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->forceDelete(); // Elimina físicamente el registro de la base de datos

        return response()->json(['message' => 'Ciudad eliminada correctamente'], 204);
    }
}
