<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciclo;
use Illuminate\Support\Facades\DB;

class CicloController extends Controller
{
    public function index($domain_id)
    {
        $areas = Ciclo::where('domain_id', $domain_id)
            ->whereNull('deleted_at')
            ->get();
        return response()->json($areas);
    }

    public function store(Request $request, $domain_id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'color' => 'string|max:255',
        ]);

        $data = $request->all();
        $data['domain_id'] = $domain_id;
        $ciclo = Ciclo::where('orden', $request->orden)
            ->where('domain_id', $domain_id)
            ->first();
        if ($ciclo) {
            return response()->json(['mensaje' => 'Ya existe un ciclo con el mismo orden', 'status' => 'exists'], 400);
        }
        if ($data['color'] == null) {
            $data['color'] = '#000000';
        }
        $area = DB::table('ciclos')->insert($data);
        return response()->json($area, 201);
    }

    public function show($id)
    {
        $area = Ciclo::find($id);
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


        $area = Ciclo::find($id);


        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }
        $ciclo = Ciclo::where('orden', $request->orden)
            ->where('domain_id', $domain_id)->where('id', '!=', $id)
            ->first();
        if ($ciclo) {
            return response()->json(['mensaje' => 'Ya existe un ciclo con el mismo orden', 'status' => 'exists'], 400);
        }

        $area->update($request->all());
        return response()->json($area);
    }

    public function destroy($domain_id, $id)
    {
        $area = Ciclo::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->delete();
        return response()->json(['mensaje' => 'Área eliminada', 'status' => 200], 200);
    }

    public function restore($id)
    {
        $area = Ciclo::withTrashed()->find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'Área no encontrada', 'status' => 404], 404);
        }

        $area->restore();
        return response()->json(['mensaje' => 'Área restaurada', 'status' => 200], 200);
    }
    public function orden(Request $request)
    {
        $id = $request->get('id');
        $nombre = $request->get('nombre');
        $color = $request->get('color');
        $orden = $request->get('orden');
        $domain_id = $request['domain_id'];
        $ciclo = Ciclo::where('orden', $orden)
            ->where('domain_id', $domain_id)
            ->first();
        if ($ciclo) {
            $ciclo->orden = null;
            $ciclo->save();

            if ($id) {
                $ciclo = Ciclo::find($id);
                $ciclo->orden = $orden;
                $ciclo->color = $color;
                $ciclo->nombre = $nombre;
                $ciclo->save();
                return response()->json(['mensaje' => 'Orden actualizado', 'status' => 200], 200);
            } else {
                Ciclo::create([
                    'nombre' => $nombre,
                    'color' => $color,
                    'orden' => $orden,
                    'domain_id' => $domain_id
                ]);
                return response()->json(['mensaje' => 'Orden actualizado', 'status' => 200], 200);
            }

        }
        return response()->json(['mensaje' => 'Ya existe un ciclo con el mismo orden', 'status' => 'exists'], 400);
    }

    public function dropDown($domain_id){
        $ciclos = Ciclo::select('id', 'nombre')->where('domain_id', $domain_id)->get();
        return response()->json($ciclos);
    }
}
