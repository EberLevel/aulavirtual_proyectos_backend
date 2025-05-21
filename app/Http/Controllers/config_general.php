<?php

namespace App\Http\Controllers;
use App\Models\Config;;
use Illuminate\Http\Request;

class config_general extends Controller
{
    public function create()
    {
    }

    public function store(Request $request)
    {
    
    }

    public function edit(Config $config)
    {
        $config = Config::first();
        return response()->json($config);
    }

    public function update(Request $request, Config $config)
    {
        $config = Config::first();

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

        $config->update($data);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}

