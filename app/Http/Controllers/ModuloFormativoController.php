<?php

namespace App\Http\Controllers;

use App\Models\ModuloFormativo;
use Illuminate\Http\Request;

class ModuloFormativoController extends Controller
{
    public function index($domain_id)
    {
        $areas = ModuloFormativo::where('domain_id', $domain_id)
                                ->whereNull('deleted_at')
                                ->get();
        return response()->json($areas);
    }

    public function store(Request $request,$domain_id)
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
        $area = ModuloFormativo::create($data);
        return response()->json($area, 201);
    }

    public function show($id)
    {
        $area = ModuloFormativo::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }
        return response()->json($area);
    }

    public function update(Request $request, $domain_id, $id)
    {

        $this->validate($request, [
            'nombre' => 'string|max:255',
            'color' => 'string|max:255',
        ]);


        $area = ModuloFormativo::find($id);


        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->update($request->all());
        return response()->json($area);
    }

    public function destroy($domain_id, $id)
    {
        $area = ModuloFormativo::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->delete();
        return response()->json(['mensaje' => 'Área eliminada', 'status' => 200], 200);
    }

    public function restore($id)
    {
        $area = ModuloFormativo::withTrashed()->find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->restore();
        return response()->json(['mensaje' => 'Área restaurada', 'status' => 200], 200);
    }
}
