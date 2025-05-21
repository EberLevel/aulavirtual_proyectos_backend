<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $evaluation = Evaluation::paginate(10);

        return response()->json($evaluation, 200);
    }

    public function byBankCv(Request $request,$id)
    {
        $evaluation = Evaluation::where('cv_bank_id', $id)->get();

        return response()->json($evaluation, 200);
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
            $request->validate([
                'cv_bank_id' => 'required',
                'user_id' => 'required',
                'position_level_id' => 'required',
                'scale_id' => 'required',
                'module' => 'required'
            ]);

            $evaluation = Evaluation::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'module' => $request->module,
                    'cv_bank_id' => $request->cv_bank_id,
                ],
                [
                    'cv_bank_id' => $request->cv_bank_id,
                    'user_id' => $request->user_id,
                    'position_level_id' => $request->position_level_id,
                    'scale_id' => $request->scale_id,
                    'module' => $request->module,
                    'especific_experience' => $request->especific_experience,
                    'general_experience' => $request->general_experience,
                    'time_validated' => $request->time_validated
            ]);

        }catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        //

        return response()->json(['evaluation' => $evaluation]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Evaluation $evaluation)
    {
        //

        return response()->json(['evaluation' => $evaluation]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evaluation $evaluation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evaluation $evaluation)
    {
        //
    }
}
