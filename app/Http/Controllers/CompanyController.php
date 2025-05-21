<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\FileTrait;
class CompanyController extends Controller
{
    use FileTrait;
    public function show($domain_id)
    {
        try {
            $company = DB::table('companies')->where('domain_id', $domain_id)->first();
           
            return response()->json(['status' => true, 'data' => $company]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

public function store(Request $request)
{
    try {
        $folderName = 'companies/'.$request->domain_id;
        
        // Datos que se actualizarán (solo el nombre inicialmente)
        $toInsert = [
            'name' => $request->nombreInstitucion,
        ];

        // Validar si se envió un logo para actualizar
        if ($request->hasFile('logo')) {
            $isValid = $this->checkIsValidImage($request->logo);
            if ($isValid) {
                // Solo sube el archivo si es válido
                $toInsert['logo_url'] = $this->uploadFile($request->logo, $folderName);
            }
        }

        // Actualizar los datos en la tabla
        DB::table('companies')
            ->where('domain_id', $request->domain_id)
            ->update($toInsert);

        return response()->json(['status' => true]);
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'error' => $e->getMessage()]);
    }
}

}
