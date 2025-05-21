<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $budgets = Budget::paginate(10);
        return response()->json(['budgets' => $budgets]);
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
        $data =  $this->validate($request, [
            'BudgetCode' => 'string|max:20',
            'PositionId' => 'binary(16)',
            'Years' => 'integer',
            'Months' => 'integer',
            'MaxSalary' => 'numeric|digits:10',
            'CurrentSalary' => 'numeric|digits:10',
            'Diference' => 'numeric|digits:10',
            'Indicators' => 'numeric|digits:10',
            'Observation' => 'text',
        ]);
        $budget = Budget::create([
            'BudgetCode' => $request->BudgetCode,
            'PositionId' => $request->PositionId,
            'Years' => $request->Years,
            'Months' => $request->Months,
            'MaxSalary' => $request->MaxSalary,
            'CurrentSalary' => $request->CurrentSalary,
            'Diference' => $request->Diference,
            'Indicators' => $request->Indicators,
            'Observation' => $request->Observation,//'binary(16)|exists:Applicants,ApplicantsId',
            /*'uuid' => Str::uuid()->toString(),
            'Item' => $request->Item,
            'DegreesStudy' => $request->DegreesStudy,
            'phone' => $request->phone,
            'email' => $request->email,
            'area_id' => $request->area_id,*/
        ]);
        return response()->json(['budget' => $budget]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $budget = Budget::findOrFail($id);
        return response()->json(['budget' => $budget]);
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
        //
        $data =  $this->validate($request, [
            'BudgetCode' => 'string|max:20',
            'PositionId' => 'binary(16)',
            'Years' => 'integer',
            'Months' => 'integer',
            'MaxSalary' => 'numeric|digits:10',
            'CurrentSalary' => 'numeric|digits:10',
            'Diference' => 'numeric|digits:10',
            'Indicators' => 'numeric|digits:10',
            'Observation' => 'text',
        ]);
        $budget = Budget::findOrFail($id);
        $budget->update([
            'BudgetCode' => $request->BudgetCode,
            'PositionId' => $request->PositionId,
            'Years' => $request->Years,
            'Months' => $request->Months,
            'MaxSalary' => $request->MaxSalary,
            'CurrentSalary' => $request->CurrentSalary,
            'Diference' => $request->Diference,
            'Indicators' => $request->Indicators,
            'Observation' => $request->Observation,//'binary(16)|exists:Applicants,ApplicantsId',
            /*'uuid' => Str::uuid()->toString(),
            'Item' => $request->Item,
            'DegreesStudy' => $request->DegreesStudy,
            'phone' => $request->phone,
            'email' => $request->email,
            'area_id' => $request->area_id,*/
        ]);
        return response()->json(['budget' => $budget]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $budget = Budget::findOrFail($id);
        $budget->delete();
        return response()->json(['message' => 'budget deleted successfully']);
    }
}
