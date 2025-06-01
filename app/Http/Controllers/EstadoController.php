<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estado;

class EstadoController extends Controller
{
    public function index($domain_id)
    {
        $areas = Estado::where('domain_id', $domain_id)
                                ->whereNull('deleted_at')
                                ->get();
        return response()->json($areas);
    }

    public function validatePayment($domain_id)
    {
        $areas = Estado::where('domain_id', $domain_id)
                                ->whereNull('deleted_at')
                                ->whereIn('id', [1, 2, 21])
                                ->get()
                                ->map(function ($estado) {
                                    return [
                                        "id" => $estado->id,
                                        "nombre" => $estado->nombre
                                    ];
                                });

        return response()->json($areas);
    }

    public function store(Request $request, $domain_id)
    {
        \Log::info('Datos de la solicitud:', $request->all()); // Depura la solicitud entrante
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'color' => 'string|max:255',
        ]);

        $data = $request->all();
        $data['domain_id'] = $domain_id;
        if ($data['color'] == null) {
            $data['color'] = '#000000';
        }
        $area = Estado::create($data);
        return response()->json($area, 201);
    }

    public function update(Request $request, $domain_id, $id)
    {
        \Log::info('Datos de la solicitud:', $request->all()); // Depura la solicitud entrante
        $this->validate($request, [
            'nombre' => 'string|max:255',
            'color' => 'string|max:255',
        ]);

        $area = Estado::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->update($request->all());
        return response()->json($area);
    }

    public function show($id)
    {
        $area = Estado::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }
        return response()->json($area);
    }



    public function destroy($domain_id, $id)
    {
        $area = Estado::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->delete();
        return response()->json(['mensaje' => 'Área eliminada', 'status' => 200], 200);
    }

    public function restore($id)
    {
        $area = Estado::withTrashed()->find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->restore();
        return response()->json(['mensaje' => 'Área restaurada', 'status' => 200], 200);
    }
}
