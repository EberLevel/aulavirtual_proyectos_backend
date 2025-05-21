<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $positions = Position::byAreaId($request->query('area_id'))->byTerm($request->query('term'))->paginate(10);
        return response()->json(['positions' => $positions]);
    }

    public function show($id)
    {
        $position = Position::findOrFail($id);
        return response()->json(['position' => $position]);
    }

    public function store(Request $request)
    {

        $data =  $this->validate($request, [
            'code' => 'required|unique:positions',
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'max_salary' => 'required|numeric',
            'current_salary' => 'required|numeric',
            'difference' => 'required|numeric',
            'area_id' => 'required|exists:areas,id',
            'institution_id' => 'required|exists:institutions,id'
        ]);

        $position = Position::create([
            'uuid' => Str::uuid()->toString(),
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'max_salary' => $request->input('max_salary'),
            'current_salary' => $request->input('current_salary'),
            'difference' => $request->input('difference'),
            'area_id' => $request->input('area_id'),
            'institution_id' => $request->input('institution_id'),
        ]);
        return response()->json(['position' => $position]);
    }

    public function update(Request $request, $id)
    {
        $data =  $this->validate($request, [
            'code' => 'required|unique:positions,code,' . $id,
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'area_id' => 'required|exists:areas,id',
        ]);

        $position = Position::findOrFail($id);
        $position->update([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'max_salary' => $request->input('max_salary'),
            'current_salary' => $request->input('current_salary'),
            'difference' => $request->input('difference'),
            'status' => $request->input('status', 'active')
        ]);
        return response()->json(['position' => $position]);
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
        return response()->json(['message' => 'position deleted successfully']);
    }
}
