<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $resumes = Resume::whereNotNull('user_id')->paginate(10);

        return response()->json(['resumes' => $resumes]);
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
                //'user_id' => 'required|unique:users,id,',
                'user_id' => 'required|integer',
                'position' => 'required|string',
                'time_to_end' => 'required|string',
                'qualification' => 'required|string',
                'total_time' => 'required|string',
                'end_date' => 'required|string',
                'p_fce' => 'required|string',
                'p_ref' => 'required|string',
                'p_unt' => 'required|string',
                'salary' => 'required|string',
                'work_mode' => 'required|string',
            ]);
            //get the user
            $user = User::find($request->user_id);

            //create the resume
            $resumen = Resume::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'position' => $request->position,
                'time_to_end' => $request->time_to_end,
                'qualification' => $request->qualification,
                'total_time' => $request->total_time,
                'end_date' => $request->end_date,
                'p_fce' => $request->p_fce,
                'p_ref' => $request->p_ref,
                'p_unt' => $request->p_unt,
                'salary' => $request->salary,
                'work_mode' => $request->work_mode,
                'created_by' => auth()->user()->id,
            ]);

        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['resumen' => $resumen], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
        $resumen = Resume::findOrFail($id);
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['resumen' => $resumen]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(resumes $resumes)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, resumes $resumes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            $resumen = Resume::findOrFail($id);
            $resumen->delete();
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['message' => 'Resumen deleted successfully']);

    }
}
