<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class ConfigGeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Obtener la primera configuración o crear una nueva si no existe
        $config = Config::firstOrNew();

        $data = $request->validate([
            'name' => 'required',
            'acronym' => 'required',
            'name_governor' => 'required',
            'cargo' => 'required',
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'background' => 'required',
            'text' => 'required',
        ]);

        if ($request->hasFile('logo')) {
            // Guardar el nuevo logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Si el modelo ya existe en la base de datos, se actualizará. De lo contrario, se creará.
        $config->fill($data)->save();

        return response()->json($config, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $config= Config::first();
        return response()->json($config, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
