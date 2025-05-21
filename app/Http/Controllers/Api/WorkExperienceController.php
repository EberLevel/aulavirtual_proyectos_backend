<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\WorkExperience;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domainId = $request->user()->domain_id;
        $work_experience = WorkExperience::paginate(10);
        return response()->json(['work_experience' => $work_experience]);
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
                'position_code' => 'nullable|string',
                'institution_type' => 'nullable|string',
                'institution' => 'nullable|string',
                'area' => 'nullable|string',
                'position' => 'nullable|string',
                'functions' => 'nullable|string',
                'employment_link_id' => 'nullable|integer',
                'position_modality_id' => 'nullable|integer',
                'salary' => 'nullable|numeric',
                'start_at' => 'nullable|date',
                'end_at' => 'nullable|date',
                'especific_experience' => 'nullable|string',
                'general_experience' => 'nullable|string',
                'countdown_days' => 'nullable|integer',
                'image' => 'nullable|file',
                'end_reason' => 'nullable|string',
                'observations' => 'nullable|string'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $imageName, 'public');
            }

            $user = User::find($request->user_id);

            $work_experience = WorkExperience::create([
                'uuid' => Str::uuid()->toString(),
                'cv_bank_id' => $request->cv_bank_id,
                'user_id' => $user->id,
                'position_code' => $request->position_code,
                'institution_type' => $request->institution_type,
                'institution' => $request->institution,
                'area' => $request->area,
                'position' => $request->position,
                'functions' => $request->functions,
                'employment_link_id' => $request->employment_link_id,
                'position_modality_id' => $request->position_modality_id,
                'salary' => $request->salary,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'especific_experience' => $request->especific_experience,
                'general_experience' => $request->general_experience,
                'countdown_days' => $request->countdown_days,
                'image' => $imagePath,
                'end_reason' => $request->end_reason,
                'observations' => $request->observations
            ]);
        } catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['work_experience' => $work_experience]);
    }

    public function byBankCv($id)
    {
        //
        $models = WorkExperience::with('employment_link', 'position_modality')->where('cv_bank_id', $id)->get();
        return response()->json(['work_experiences' => $models]);
    }


    public function validateRegister(Request $request,$id){
        $workExperience= WorkExperience::find($id);
         if($workExperience){
              $workExperience->validated = $request->validated;
              $workExperience->save();
              return response()->json(['message' => 'Experiencia laboral actualizado correctamente', 'data' => $workExperience], 200);
         }

         return response()->json(['message' => 'Experiencia laboral no encontrado'], 404);
    }

    public function getDataCreate()
    {

        $positions = \App\Models\Position::all();
        $employment_links = \App\Models\VinculoLaboral::all();
        $position_modalities = \App\Models\ModalidadPuesto::all();

        return response()->json(['positions' => $positions, 'employment_links' => $employment_links, 'position_modalities' => $position_modalities]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $work_experience = WorkExperience::findOrFail($id);
        return response()->json(['work_experience' => $work_experience]);
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
    public function updateData(Request $request,$id)
    {
        $this->validate($request, [
            'position_code' => 'nullable|string',
            'institution_type' => 'nullable|string',
            'institution' => 'nullable|string',
            'area' => 'nullable|string',
            'position' => 'nullable|string',
            'functions' => 'nullable|string',
            'employment_link_id' => 'nullable|integer',
            'position_modality_id' => 'nullable|integer',
            'salary' => 'nullable|numeric',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'especific_experience' => 'nullable|string',
            'general_experience' => 'nullable|string',
            'countdown_days' => 'nullable|integer',
            'image' => 'nullable|file',
            'end_reason' => 'nullable|string',
            'observations' => 'nullable|string'
        ]);


        Log::info($id);

        $work_experience = WorkExperience::findOrFail($id);
        if (!$work_experience) {
            return response()->json(['message' => 'Experiencia laboral no encontrado'], 404);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');

            $work_experience->image= $imagePath;
        }

        $work_experience->update([
            'position_code' => $request->position_code,
            'institution_type' => $request->institution_type,
            'institution' => $request->institution,
            'area' => $request->area,
            'position' => $request->position,
            'functions' => $request->functions,
            'employment_link_id' => $request->employment_link_id,
            'position_modality_id' => $request->position_modality_id,
            'salary' => $request->salary,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'especific_experience' => $request->especific_experience,
            'general_experience' => $request->general_experience,
            'countdown_days' => $request->countdown_days,
            'end_reason' => $request->end_reason,
            'observations' => $request->observations
        ]);


        return response()->json(['message' => 'Experiencia laboral actualizado correctamente', 'data' => $work_experience], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $work_experience = WorkExperience::findOrFail($id);
            $work_experience->delete();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['message' => 'work experience deleted successfully']);
    }
}
