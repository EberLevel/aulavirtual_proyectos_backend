<?php

namespace App\Http\Controllers;

use App\Models\EvaluationFinal;
use Illuminate\Http\Request;

class EvaluationFinalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    
    public function byBankCv(Request $request,$id)
    {
        $evaluation = EvaluationFinal::where('cv_bank_id', $id)->get();

        return response()->json($evaluation, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* 
                'cv_bank_id',
        'user_id',
        'module_status_management',
        'action_id',
        'aceptation_id',
        'position_level_id',
        'scale_id',
        'institution'
        */
    
            $request->validate([
                'cv_bank_id' => 'required',
                'user_id' => 'required',
                'module_status_management' => 'required',
                'action_id' => 'required',
                'aceptation_id' => 'required',
                'position_level_id' => 'required',
                'scale_id' => 'required',
                'institution' => 'required'
            ]);

            $evaluation = EvaluationFinal::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'cv_bank_id' => $request->cv_bank_id,
                ],
                [
                    'cv_bank_id' => $request->cv_bank_id,
                    'user_id' => $request->user_id,
                    'module_status_management' => $request->module_status_management,
                    'action_id' => $request->action_id,
                    'aceptation_id' => $request->aceptation_id,
                    'position_level_id' => $request->position_level_id,
                    'scale_id' => $request->scale_id,
                    'institution' => $request->institution
            ]);

    
        //

        return response()->json(['evaluation' => $evaluation]);

    }

    /**
     * Display the specified resource.
     */
    public function show(EvaluationFinal $evaluationFinal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EvaluationFinal $evaluationFinal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EvaluationFinal $evaluationFinal)
    {
        //
    }
}
