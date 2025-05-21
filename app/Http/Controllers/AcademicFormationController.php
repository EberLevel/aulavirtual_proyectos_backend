<?php

namespace App\Http\Controllers;

use App\Models\AcademicFormation;
use App\Models\EstadoActual;
use App\Models\EstadoAvance;
use App\Models\GradoInstruccion;
use App\Models\Institution;
use App\Models\NivelCargo;
use App\Models\Profesion;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademicFormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domainId = $request->user()->domain_id;
        $academics = AcademicFormation::whereNotNull('user_id')->paginate(10);
        return response()->json(['academic' => $academics]);
    }


    public function getDataCreate()
    {
        $educationlDegree = GradoInstruccion::all();
        $professions = Profesion::all();
        $estadoActuales = EstadoActual::all();
        $estadoAvances = EstadoAvance::all();
        $institutions = Institution::all();
        $positionLevel = NivelCargo::all();

        return response()->json([
            'educationlDegrees' => $educationlDegree,
            'professions' => $professions,
            'estadoActuales' => $estadoActuales,
            'estadoAvances' => $estadoAvances,
            'institutions' => $institutions,
            'positionLevels' => $positionLevel,
        ]);

    }


    public function byBankCv($id)
    {
        //
        $academics = AcademicFormation::with(['professionModel','educationDegree','advanceStatus'])
                                        ->where('cv_bank_id', $id)->get();
        return response()->json(['academic_formations' => $academics]);
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
                'user_id' => 'required|integer',
                'cv_bank_id' => 'required|integer',
                'education' => 'required|string|max:200',
                'profession' => 'required|string|max:200',
                'con' => 'nullable|string|max:200',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'required|string|in:En curso,Finalizado',
                'advance' => 'required|string|max:200',
                'institute' => 'required|string|max:200',
                'date_start' => 'required|date',
                'date_end' => 'required|date',
                'observation' => 'required|nullable|string',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $imageName, 'public');
            }

            $user = User::find($request->user_id);

            $academic = AcademicFormation::create([
                'cv_bank_id' => $request->cv_bank_id,
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'education' => $request->education,
                'profession' => $request->profession,
                'con' => $request->con,
                'image' => $imagePath,
                'status' => $request->status,
                'advance' => $request->advance,
                'institute' => $request->institute,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'observation' => $request->observation
            ]);
        }catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['academic' => $academic]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
           $academic = AcademicFormation::findOrFail($id);
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['academic' => $academic]);
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
        try {
        //
        $this->validate($request, [
            'user_id' => 'required|integer',
            'cv_bank_id' => 'required|integer',
            'education' => 'required|string|max:200',
            'profession' => 'required|string|max:200',
            'con' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|string|in:En curso,Finalizado',
            'advance' => 'required|string|max:200',
            'institute' => 'required|string|max:200',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'observation' => 'required|nullable|string',
        ]);

        $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $imageName, 'public');
            }

        $academic = AcademicFormation::findOrFail($id);

        $academic->update([
            'user_id' => $request->user_id,
            'level_academic' => $request->level_academic,
            'carrer' => $request->carrer,
            'status' => $request->status,
            'advance' => $request->advance,
            'month' => $request->month,
            'years' => $request->years,
            'institute' => $request->institute,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'observation' => $request->observation,
        ]);

        if ($imagePath) {
            $academic->update(['image' => $imagePath]);
        }

        }catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['academic' => $academic]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $academic = AcademicFormation::findOrFail($id);
            $academic->delete();
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['message' => 'academic deleted successfully']);
    }


    public function validateRegister(Request $request,$id){
        $academicFormation= AcademicFormation::find($id);
         if($academicFormation){
              $academicFormation->validated = $request->validated;
              $academicFormation->save();
              return response()->json(['message' => 'Formación academica actualizada correctamente', 'data' => $academicFormation], 200);
         }

         return response()->json(['message' => 'Formación academica no encontrada'], 404);
    }
}
