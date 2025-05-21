<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domainId = $request->user()->domain_id;
        $reference = Reference::paginate(10);

        return response()->json($reference, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function getDataCreate()
    {
        $data =[];
        return response()->json(['data' => $data]);
    }

    
    public function byBankCv(Request $request,$id)
    {
        //
        $models = Reference::where('cv_bank_id', $id)
                             ->byType($request->type)
                             ->get();
                             
        return response()->json(['references' => $models]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            
            $this->validate($request, [
                'user_id' => 'required|integer|exists:users,id',
                'cv_bank_id' => 'required|integer|exists:cv_banks,id',
                'description' => 'required|string|max:200',
                'phone' => 'required|string|max:200',
                'ocupation' => 'required|string|max:200',
                'type'=>'required|in:personal,laboral,recomendacion',
            ]);

            $reference = Reference::create([
                'user_id' => $request->user_id,
                'cv_bank_id' => $request->cv_bank_id,
                'description' => $request->description,
                'phone' => $request->phone??null,
                'ocupation' => $request->ocupation??null,
                'reason' => $request->reason??null,
                'type'=>$request->type
            ]);

        }catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        //

        return response()->json(['reference' => $reference]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reference = Reference::findOrFail($id);
        return response()->json(['reference' => $reference]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reference $reference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data =  $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id',
            'cv_bank_id' => 'required|integer|exists:cv_banks,id',
            'description' => 'required|string|max:200',
            'phone' => 'required|string|max:200',
            'ocupation' => 'required|string|max:200',
            'type'=>'required|in:personal,laboral,recomendacion',
        ]);

        $reference = Reference::findOrFail($id);
        if (!$reference) {
            return response()->json(['message' => 'Referencia no encontrada'], 404);
        }
        $reference->update([
            'description' => $request->description,
            'phone' => $request->phone,
            'reason' => $request->reason,
            'ocupation' => $request->ocupation
        ]);
        
        return response()->json(['message' => 'Referencia actualizada correctamente', 'data' => $reference], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reference = Reference::find($id);

        if (!$reference) {
            return response()->json(['message' => 'Referencia no encontrada'], 404);
        }

        $reference->delete();

        return response()->json(['message' => 'Referencia eliminada correctamente'], 204);
    }
}
