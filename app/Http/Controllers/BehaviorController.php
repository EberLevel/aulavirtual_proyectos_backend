<?php

namespace App\Http\Controllers;

use App\Models\Behavior;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BehaviorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $behavior = Behavior::paginate(10);

        return response()->json($behavior, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'type_document_id' => 'required|integer',
                'document_number' => 'string|max:100',
                'activity_name' => 'string|max:100',
                'description' => 'string|max:100',
                'date' => 'string|max:100',
                'resources' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'observation' => 'string|max:100',
                'escala_id' => 'required|integer',
                'average_behaviors' => 'string|max:20',
            ]);

            $imagePath = null;
            if ($request->hasFile('resources')) {
                $image = $request->file('resources');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $imageName, 'public');
            }

            $behavior = Behavior::create([
                'type_document_id' => $request->type_document_id,
                'document_number' => $request->document_number,
                'activity_name' => $request->activity_name,
                'description' => $request->description,
                'date' => $request->date,
                'resources' => $imagePath,
                'observation' => $request->observation,
                'escala_id' => $request->escala_id,
                'average_behaviors' => $request->average_behaviors
            ]);
        }catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        //

        return response()->json(['behavior' => $behavior]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $behavior = Behavior::findOrFail($id);
        return response()->json(['behavior' => $behavior]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Behavior $behavior)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data =  $this->validate($request, [
            'type_document_id' => 'required|integer',
            'document_number' => 'string|max:100',
            'activity_name' => 'string|max:100',
            'description' => 'string|max:100',
            'date' => 'string|max:100',
            'resources' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'observation' => 'string|max:100',
            'escala_id' => 'required|integer',
            'average_behaviors' => 'string|max:20',
        ]);

        $imagePath = null;
            if ($request->hasFile('resources')) {
                $image = $request->file('resources');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $imageName, 'public');
            }

        $behavior = Behavior::findOrFail($id);
        if (!$behavior) {
            return response()->json(['message' => 'Comportamiento no encontrado'], 404);
        }
        $behavior->update([
            'type_document_id' => $request->type_document_id,
            'document_number' => $request->document_number,
            'activity_name' => $request->activity_name,
            'description' => $request->description,
            'date' => $request->date,
            'resources' => $imagePath,
            'observation' => $request->observation,
            'escala_id' => $request->escala_id,
            'average_behaviors' => $request->average_behaviors
        ]);
        
        return response()->json(['message' => 'Comportamiento actualizado correctamente', 'data' => $behavior], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $behavior = Behavior::find($id);

        if (!$behavior) {
            return response()->json(['message' => 'Comportamiento no encontrado'], 404);
        }

        $behavior->delete();

        return response()->json(['message' => 'Comportamiento eliminado correctamente'], 204);
    }
}
