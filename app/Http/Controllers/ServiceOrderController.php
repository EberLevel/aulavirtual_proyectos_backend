<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ServiceOrderController extends Controller
{
    public function index($cv_bank_id)
    {
        $service_order = ServiceOrder::where('cv_bank_id', $cv_bank_id)->paginate(10);

        return response()->json($service_order, 200);
    }

    public function store(Request $request)
    {

        try {
            $this->validate($request, [
                'cv_bank_id' => 'required|integer|exists:cv_banks,id',
                'proveedor' => 'required',
                'valor' => 'required'
            ]);


            $service_order = ServiceOrder::create([
                'number_order'  => $request->number_order,
                'exp_siaf'      => $request->exp_siaf,
                'certif_siaf'   => $request->certif_siaf,
                'fecha'         => $request->fecha,
                'proveedor'     => $request->proveedor,
                'concepto'      => $request->concepto,
                'mon'           => $request->mon,
                'valor'         => $request->valor,
                'state_id'      => $request->state_id,
                'cv_bank_id'    => $request->cv_bank_id
            ]);
        } catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['service_order' => $service_order]);
    }

    public function update(Request $request, $id)
    {

        try {
            $this->validate($request, [
                'cv_bank_id' => 'required|integer|exists:cv_banks,id',
                'proveedor' => 'required',
                'valor' => 'required'
            ]);

            ServiceOrder::where('id', $id)->update([
                'number_order'  => $request->number_order,
                'exp_siaf'      => $request->exp_siaf,
                'certif_siaf'   => $request->certif_siaf,
                'fecha'         => $request->fecha,
                'proveedor'     => $request->proveedor,
                'concepto'      => $request->concepto,
                'mon'           => $request->mon,
                'valor'         => $request->valor,
                'state_id'      => $request->state_id,
                'cv_bank_id'    => $request->cv_bank_id
            ]);

            $service_order = ServiceOrder::find($id);
        } catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['message' => 'Orden de Servicio actualizado correctamente', 'data' => $service_order], 200);
    }

    public function destroy($id)
    {
       // dd($id);
        $service = ServiceOrder::find($id);

        if (!$service) {
            return response()->json(['message' => 'Orden de Servicio no encontrado'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Orden de Servicio eliminado correctamente'], 204);
    }
}
