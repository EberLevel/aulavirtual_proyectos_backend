<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AreaController extends Controller
{
    public function index(Request $request)
    {

        $areas = Area::whereNull('parent_id')
                        ->byInstitutionId($request->query('institution_id'))
                        ->byTerm($request->term)
                        ->paginate(10);
        return response()->json(['areas' => $areas]);
    }

    public function subareas(Request $request, $id)
    {

        $area = Area::findOrFail($id);
        $institution = Institution::findOrFail($request->query('institution_id'));

        $areas = Area::where('parent_id', $id)
                                 ->byInstitutionId($request->query('institution_id'))
                                 ->byTerm($request->term)
                                 ->paginate(10);
        return response()->json(['areas' => $areas, 'area' => $area, 'institution' => $institution]);
    }

    public function show($id)
    {
        $area = Area::findOrFail($id);
        $institution = Institution::findOrFail($area->institution_id);
        return response()->json(['area' => $area, 'institution' => $institution]);
    }

    public function store(Request $request)
    {
        $data =  $this->validate($request, [
            'code' => 'required|unique:areas',
            'name' => 'required|string',
            'short_name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'ubigeo' => 'required',
            'institution_id' => 'required|exists:institutions,id',
            'parent_id' => 'nullable|exists:areas,id'
        ]);

        $level=1;

        if($request->parent_id){
            $parent = Area::findOrFail($request->parent_id);
            $level = $parent->level+1;
        }

        $area = Area::create([
            'uuid' => Str::uuid()->toString(),
            'code' => $request->code,
            'name' => $request->name,
            'short_name' => $request->short_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->input('address', null),
            'ubigeo' => $request->input('ubigeo', null),
            'parent_id' => $request->input('parent_id',null),
            'institution_id' => $request->input('institution_id',null),
            'level' => $level,
            'created_by' => auth()->user()->id
        ]);

        return response()->json(['area' => $area]);
    }

    public function update(Request $request, $id)
    {
        $data =  $this->validate($request, [
            'code' => 'required|unique:areas,code,' . $id,
            'name' => 'required|string',
            'short_name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'ubigeo' => 'required'
        ]);

        $area = Area::findOrFail($id);
        $area->update([
            'code' => $request->code,
            'name' => $request->name,
            'short_name' => $request->short_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->input('address', null),
            'ubigeo' => $request->input('ubigeo', null)
        ]);
        return response()->json(['area' => $area]);
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json(['message' => 'area deleted successfully']);
    }
}
