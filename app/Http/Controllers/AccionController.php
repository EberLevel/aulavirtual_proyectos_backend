<?php

namespace App\Http\Controllers;

use App\Models\AccionOi;
use Illuminate\Http\Request;

class AccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        $accion = AccionOi::where('domain_id', $domain_id)
            ->whereNull('deleted_at')
            ->get();
        return response()->json($accion);
    }

    public function store(Request $request, $domain_id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'color' => 'string|max:255',
        ]);

        $data = $request->all();
        $data['domain_id'] = $domain_id;
        if ($data['color'] == null) {
            $data['color'] = '#000000';
        }
        $accion = AccionOi::create($data);
        return response()->json($accion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $accion = AccionOi::find($id);
        if (!$accion) {
            return response()->json(['mensaje' => 'Gestion no encontrada', 'status' => 404], 404);
        }
        return response()->json($accion);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'string|max:255',
            'color' => 'string|max:255',
        ]);
        $accion = AccionOi::find($id);

        if (!$accion) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }
        $accion->update($request->all());
        return response()->json($accion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $accion = AccionOi::find($id);
        if (!$accion) {
            return response()->json(['mensaje' => 'Gestion no encontrada', 'status' => 404], 404);
        }
        $accion->delete();
        return response()->json(['mensaje' => 'Gestion eliminada'], 200);
    }

    public function restore($id)
    {
        $accion = AccionOi::withTrashed()->find($id);
        if (!$accion) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $accion->restore();
        return response()->json(['mensaje' => 'Área restaurada', 'status' => 200], 200);
    }
}
