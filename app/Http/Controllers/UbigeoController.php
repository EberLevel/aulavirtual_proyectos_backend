<?php

namespace App\Http\Controllers;

use App\Models\UbigeoPeruDepartment;
use App\Models\UbigeoPeruProvince;

// use App\Models\UbigeoPeruDepartment;
// use App\Models\UbigeoPeruProvince;

class UbigeoController extends Controller
{
    // Obtener todos los departamentos
    public function departamentos()
    {
        $departamentos = UbigeoPeruDepartment::all();
        return response()->json($departamentos);
    }

    // Obtener las provincias de un departamento específico
    public function provincias($departamento_id)
    {
        $departamento = UbigeoPeruDepartment::with('provinces')->find($departamento_id);

        if (!$departamento) {
            return response()->json(['message' => 'Departamento no encontrado'], 404);
        }

        return response()->json($departamento->provinces);
    }

    // Obtener los distritos de una provincia específica dentro de un departamento
    public function distritos($departamento_id, $provincia_id)
    {
        $provincia = UbigeoPeruProvince::where('department_id', $departamento_id)
            ->with('districts')
            ->find($provincia_id);

        if (!$provincia) {
            return response()->json(['message' => 'Provincia no encontrada en el departamento'], 404);
        }

        return response()->json($provincia->districts);
    }
}