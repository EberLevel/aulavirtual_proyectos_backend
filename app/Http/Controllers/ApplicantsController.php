<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Applicants;
use Illuminate\Http\Request;

class ApplicantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $applicants = Applicants::paginate(10);
        return response()->json(['applicants' => $applicants]);
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
            'StaffId' => 'binary(16)',
            'ManuelCode' => 'string|max:20',
            'DocumnetType' => 'string|max:20',
            'DocumentNumber' => 'string|max:20',
            'Photo' => 'string|max:100',
            'Names' => 'string|max:200',
            'Phone' => 'string|max:20',
            'CivilStatus' => 'string|max:20',
            'Children' => 'integer',
            'BirthDate' => 'date',
            'Age' => 'integer',
            'DegreeInstruction' => 'string|max:20',
            'Profession' => 'string|max:20',
            'ProfessionOther' => 'string|max:100',
            'Email' => 'string|max:100',
            'Url' => 'string|max:100',
            'ProfileProfessional' => 'string|max:100',
            //'ApplicatId' => 'binary(16)|exists:Applicants,ApplicantsId',
        ]);
        $applicant = Applicants::create([
            'StaffId' => $request->StaffId,
            'ManuelCode' => $request->ManuelCode,
            'DocumnetType' => $request->DocumnetType,
            'DocumentNumber' => $request->DocumentNumber,
            'Photo' => $request->Photo,
            'Names' => $request->Names,
            'Phone' => $request->Phone,
            'CivilStatus' => $request->CivilStatus,
            'Children' => $request->Children,
            'BirthDate' => $request->BirthDate,
            'Age' => $request->Age,
            'DegreeInstruction' => $request->DegreeInstruction,
            'Profession' => $request->Profession,
            'ProfessionOther' => $request->ProfessionOther,
            'Email' => $request->Email,
            'Url' => $request->Url,
            'ProfileProfessional' => $request->ProfileProfessional,//'binary(16)|exists:Applicants,ApplicantsId',
            /*'uuid' => Str::uuid()->toString(),
            'Item' => $request->Item,
            'DegreesStudy' => $request->DegreesStudy,
            'phone' => $request->phone,
            'email' => $request->email,
            'area_id' => $request->area_id,*/
        ]);
        return response()->json(['applicant' => $applicant]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $applicant = Applicants::findOrFail($id);
        return response()->json(['applicant' => $applicant]);
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
            'StaffId' => 'binary(16)',
            'ManuelCode' => 'string|max:20',
            'DocumnetType' => 'string|max:20',
            'DocumentNumber' => 'string|max:20',
            'Photo' => 'string|max:100',
            'Names' => 'string|max:200',
            'Phone' => 'string|max:20',
            'CivilStatus' => 'string|max:20',
            'Children' => 'integer',
            'BirthDate' => 'date',
            'Age' => 'integer',
            'DegreeInstruction' => 'string|max:20',
            'Profession' => 'string|max:20',
            'ProfessionOther' => 'string|max:100',
            'Email' => 'string|max:100',
            'Url' => 'string|max:100',
            'ProfileProfessional' => 'string|max:100',
            //'ApplicatId' => 'binary(16)|exists:Applicants,ApplicantsId',
        ]);
        $applicant = Applicants::findOrFail($id);
        $applicant->update([
            'StaffId' => $request->StaffId,
            'ManuelCode' => $request->ManuelCode,
            'DocumnetType' => $request->DocumnetType,
            'DocumentNumber' => $request->DocumentNumber,
            'Photo' => $request->Photo,
            'Names' => $request->Names,
            'Phone' => $request->Phone,
            'CivilStatus' => $request->CivilStatus,
            'Children' => $request->Children,
            'BirthDate' => $request->BirthDate,
            'Age' => $request->Age,
            'DegreeInstruction' => $request->DegreeInstruction,
            'Profession' => $request->Profession,
            'ProfessionOther' => $request->ProfessionOther,
            'Email' => $request->Email,
            'Url' => $request->Url,
            'ProfileProfessional' => $request->ProfileProfessional,//'binary(16)|exists:Applicants,ApplicantsId',
            /*'uuid' => Str::uuid()->toString(),
            'Item' => $request->Item,
            'DegreesStudy' => $request->DegreesStudy,
            'phone' => $request->phone,
            'email' => $request->email,
            'area_id' => $request->area_id,*/
        ]);
        return response()->json(['applicant' => $applicant]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $applicant = Applicants::findOrFail($id);
        $applicant->delete();
        return response()->json(['message' => 'applicant deleted successfully']);
    }
}
