<?php

namespace App\Http\Controllers;

use App\Models\GrupoDeEvaluaciones;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GrupoDeEvaluacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $curso_id
     * @return \Illuminate\Http\Response
     */
    public function index($curso_id)
    {   
        $grupos = GrupoDeEvaluaciones::withTrashed()
        ->where('curso_id', $curso_id)
        ->whereNull('deleted_at')
        ->get();
        
        return response()->json($grupos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // No se necesita implementación para API
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
            'curso_id' => 'required|exists:cursos,id',
            'nombre_del_grupo' => 'required|string|max:255',
        ]);
        
    
        $grupo = GrupoDeEvaluaciones::create($validatedData);
        return response()->json($grupo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $grupo = GrupoDeEvaluaciones::withTrashed()->findOrFail($id);
        return response()->json($grupo);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // No se necesita implementación para API
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, [
            'nombre_del_grupo' => 'required|string|max:255',
        ]);

        $grupo = GrupoDeEvaluaciones::withTrashed()->findOrFail($id);
        $grupo->update($validatedData);
        return response()->json($grupo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $grupo = GrupoDeEvaluaciones::withTrashed()->findOrFail($id);
        $grupo->delete();
    
        return response()->json(['message' => 'Curso eliminado exitosamente'], 201);
    }
}