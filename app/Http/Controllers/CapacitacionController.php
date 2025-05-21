<?php

namespace App\Http\Controllers;

use App\Models\Capacitacion;
use App\Models\Docente;
use Illuminate\Http\Request;

class CapacitacionController extends Controller
{
    public function index()
    {
        $capacitaciones = Capacitacion::where('estado', 1)->get();
        if ($capacitaciones) {
            return response()->json([
                'responseCode' => 0,
                'response' => $capacitaciones
            ], 200);
        }
        return response()->json('Record not found', 404);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:25',
            'nombre' => 'required|string|max:255',
            'horas' => 'required|integer',
            'sylabus' => 'required|string',
            'temas' => 'required|string',
            'idEstado' => 'required',
            'docente' => 'required|integer',
            'fecha' => 'required',
        ]);
        $request->merge(['estado' => 1]);
        $capacitacion = Capacitacion::create($request->all());
        return response()->json($capacitacion, 201);
    }

    public function show($id)
    {
        $capacitacion = Capacitacion::find($id);
        if (is_null($capacitacion)) {
            return response()->json('Record not found', 404);
        }
        return response()->json($capacitacion, 200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:25',
            'nombre' => 'required|string|max:255',
            'horas' => 'required|integer',
            'sylabus' => 'required|string',
            'temas' => 'required|string',
            'idEstado' => 'required',
            'docente' => 'required|integer',
            'fecha' => 'required',
        ]);
        $capacitacion = Capacitacion::find($id);
        if (is_null($capacitacion)) {
            return response()->json('Record not found', 404);
        }
        $capacitacion->update($request->all());
        return response()->json($capacitacion, 200);
    }

    public function destroy($id)
    {
        $capacitacion = Capacitacion::find($id);
        if (is_null($capacitacion)) {
            return response()->json('Record not found', 404);
        }
        $capacitacion->estado = 0;
        $capacitacion->save();
        return response()->json($capacitacion, 200);
    }

    public function generateCode()
    {
        $count = Capacitacion::count() + 1;
        $codigo = 'CAP-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        return response()->json(['code' => $codigo], 200);
    }


    public function listarDocentes()
    {
        $docentes = Docente::all();
        if ($docentes) {
            return response()->json([
                'responseCode' => 0,
                'response' => $docentes
            ], 200);
        }
        return response()->json('Record not found', 404);
    }
}
