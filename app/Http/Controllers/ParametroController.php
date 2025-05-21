<?php

namespace App\Http\Controllers;

use App\Models\TGParametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class ParametroController extends Controller
{
    public function index(Request $request)
    {
       // if (!$request) {
      //      return response()->json('Send the parameter of domain');
      //  }
      //  $dominio = $request->input('id');
        $txNombre = $request->input('tx_nombre');

        
     //   Log::info('Valor de dominio: ' . $dominio);

        $parametros = TGParametro::where('tx_nombre', $txNombre)->whereNull('deleted_at')->get();
      

        if ($parametros) {
            return response()->json($parametros);
        }
        return response()->json('Record not found', 404);
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'tx_nombre' => 'required|string|max:255',
            'tx_abreviatura' => 'required|string|max:255',
            'tx_item_description' => 'required|string|max:255',
            'domain_id' => 'required',
        ]);
        $count = TGParametro::where('tx_nombre', $request->tx_nombre)
        ->where('tx_abreviatura', $request->tx_abreviatura)
        ->count();

        // Incrementar el valor del conteo en 1
        $request->merge(['nu_item' => $count + 1]);
        $parametro = TGParametro::create($request->all());
        return response()->json($parametro, 201);
    }

    public function show($id)
    {
        $parametro = TGParametro::where('id', $id)
        ->whereNull('deleted_at')
        ->first();
           if (is_null($parametro)) {
            return response()->json('Record not found', 404);
        }
        return response()->json($parametro, 200);
    }

  

    public function update(Request $request, $id)
    {
        $this->validate($request, [
             'tx_nombre' => 'required|string|max:255',
            'tx_abreviatura' => 'required|string|max:255',
            'tx_item_description' => 'required|string|max:255',
            'domain_id' => 'required',
        ]);
        $parametro = TGParametro::find($id);
        if (is_null($parametro)) {
            return response()->json('Record not found', 404);
        }
        $parametro->update($request->all());
        return response()->json($parametro, 200);
    }
    public function destroy($id)
    {
   
        $parametro = TGParametro::find($id);
    if (is_null($parametro)) {
        return response()->json('Record not found', 404);
    }

    // Asumiendo que tienes acceso al ID del usuario actual
   // $userId = auth()->user()->id;

    // Actualizar los campos deleted_at y deleted_by
    $parametro->deleted_at = Carbon::now();
    $parametro->save();

    return response()->json(['message' => 'Eliminado correctamente'], 201);
    }


    public function indexAll($domain_id)
    {
        $parametros = TGParametro::select('tx_nombre', 'tx_abreviatura')
        ->whereNull('deleted_at')
        ->where('domain_id', $domain_id)
        ->groupBy('tx_nombre', 'tx_abreviatura')
        ->get();

    return response()->json($parametros);
    }
    public function indexRecursive($domain_id)
    {
        $parametros = TGParametro::join('domains', 'domains.id', '=', 't_g_parametros.domain_id')
        ->select('t_g_parametros.nu_id_parametro','t_g_parametros.nu_item', 't_g_parametros.tx_item_description', 't_g_parametros.tx_abreviatura', 't_g_parametros.tx_nombre', 'domains.nombre','t_g_parametros.color')
        ->whereNull('t_g_parametros.deleted_at')
        ->where('t_g_parametros.domain_id', $domain_id)
        ->groupBy('t_g_parametros.tx_nombre', 't_g_parametros.tx_abreviatura', 't_g_parametros.nu_item', 't_g_parametros.tx_item_description', 'domains.nombre','t_g_parametros.nu_id_parametro')
        ->get();

    return response()->json($parametros);
    }
    public function dropdown($domain_id)      
    {
        //select TXT_NOMBRE ,NU_ID_PARAMETRO from tg_parametros 
        $parametros = DB::table('t_g_parametros')
        ->select('tx_nombre', 'nu_id_parametro')->where('domain_id', $domain_id)->get();
        return response()->json($parametros);
    }
}
