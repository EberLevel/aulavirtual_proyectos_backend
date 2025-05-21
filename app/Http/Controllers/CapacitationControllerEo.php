<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Capacitation;
use Illuminate\Http\Request;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CapacitationControllerEo extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domainId = $request->user()->domain_id;

        $capacitations = Capacitation::paginate(10);
        return response()->json(['capacitations' => $capacitations]);
    }

    public function byBankCv($id)
    {
        //
            $academics = Capacitation::with(['advanceStatus','typeCapacitation'])->where('cv_bank_id', $id)->get();
        return response()->json(['capacitations' => $academics]);
    }

    public function getDataCreate(){
        $estadoActuales = \App\Models\EstadoActual::all();
        $estadoAvances = \App\Models\EstadoAvance::all();
        $tipoCapacitaciones = \App\Models\TipoCapacitacion::all();
        return response()->json([
            'estadosActuales' => $estadoActuales,
            'estadoAvances' => $estadoAvances,
            'tipoCapacitaciones' => $tipoCapacitaciones
        ]);
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
        //
        try {

            $this->validate($request, [
                'cv_bank_id' => 'required|integer',
                'user_id' => 'required|integer',
                'status' => 'required',
                'advance' => 'required|string|max:200',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'insitution' => 'required|string|max:200',
                'date_start' => 'required|date',
                'date_end' => 'required|date',
                'time' => 'required|string|max:200',
                //'type' => 'required|string|max:200',
                //'level_position' => 'required|string|max:200',
                //'score' => 'required',
                'observation' => 'nullable|string'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $imageName, 'public'); // Almacenar la imagen en la carpeta 'public/images'
            }

            $user = User::find($request->user_id);

            $capacitation = Capacitation::create([
                'uuid' => Str::uuid()->toString(),
                'cv_bank_id' => $request->cv_bank_id,
                'user_id' => $user->id,
                'status' => $request->status,
                'advance' => $request->advance,
                'image' => $imagePath,
                'insitution' => $request->insitution,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'time' => $request->time,
                'type' => null,
                'level_position' => null,
                'score' => null,
                'observation' => $request->observation
            ]);

        }catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['capacitation' => $capacitation]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $capacitation = Capacitation::findOrFail($id);
        return response()->json(['capacitation' => $capacitation]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data =  $this->validate($request, [
            'cv_bank_id' => 'required|integer',
            'user_id' => 'required|integer',
            'status' => 'required',
            'advance' => 'required|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'insitution' => 'required|string|max:200',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'time' => 'required|string|max:200',
            // 'type' => 'required|string|max:200',
            // 'level_position' => 'required|string|max:200',
            // 'score' => 'required',
            'observation' => 'nullable|string'
        ]);

        $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $imageName, 'public');
            }

        $capacitation = Capacitation::findOrFail($id);
        if (!$capacitation) {
            return response()->json(['message' => 'Capacitación no encontrado'], 404);
        }
        $capacitation->update([
            'cv_bank_id' => $request->cv_bank_id,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'advance' => $request->advance,
            'insitution' => $request->insitution,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'time' => $request->time,
            'type' => $request->type,
            'level_position' => $request->level_position,
            'score' => $request->score,
            'observation' => $request->observation
        ]);

        if ($imagePath) {
            $capacitation->update(['image' => $imagePath]);
        }

        return response()->json(['message' => 'Capacitación actualizada correctamente', 'data' => $capacitation], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $capacitation = Capacitation::findOrFail($id);
            $capacitation->delete();
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['message' => 'capacitation deleted successfully']);
    }

    public function validateRegister(Request $request,$id){
        $capacitation= Capacitation::find($id);
         if($capacitation){
              $capacitation->validated = $request->validated;
                $capacitation->save();
              return response()->json(['message' => 'Capacitación actualizado correctamente', 'data' => $capacitation], 200);
         }

         return response()->json(['message' => 'Capacitación no encontrado'], 404);
    }
}
