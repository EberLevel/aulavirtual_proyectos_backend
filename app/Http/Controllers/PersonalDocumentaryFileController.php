<?php

namespace App\Http\Controllers;

use App\Models\PersonalDocumentaryFile;
use App\Models\TipoDocumentoDeGestion;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PersonalDocumentaryFileController extends Controller
{
    public function index(Request $request, $cv_bank_id)
    {
        $document_file = PersonalDocumentaryFile::with('managementDocumentType')->where('cv_bank_id', $cv_bank_id);

        if ($request->has('activity_name')) {
            $activity_name  = $request->input('activity_name');
            $document_file  = $document_file->where('activity_name', 'like', '%' . $activity_name . '%');
        }

        if ($request->has('document_number')) {
            $document_number  = $request->input('document_number');
            $document_file  = $document_file->where('document_number', 'like', '%' . $document_number . '%');
        }


        $document_file = $document_file->paginate(10);

        return response()->json($document_file, 200);
    }


    public function dataCreate()
    {
        $management_document_types = TipoDocumentoDeGestion::all();
        return response()->json(['management_document_types' => $management_document_types], 200);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'activity_name' => 'required',
                'document_number' => 'required',
                'management_document_type_id' => 'required|integer|exists:tipo_documento_de_gestion,id',
            ]);

            $imagePath = null;

            if ($request->hasFile('resources')) {
                $image = $request->file('resources');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $imageName, 'public');
            }

            $behavior = PersonalDocumentaryFile::create([
                'management_document_type_id'   => $request->management_document_type_id,
                'activity_name'                 => $request->activity_name,
                'document_number'               => $request->document_number,
                'description'                   => $request->description,
                'observations'                  => $request->observations,
                'fecha'                         => $request->fecha,
                'resources'                     => $imagePath,
                'cv_bank_id'                    => $request->cv_bank_id
            ]);
        } catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        //

        return response()->json(['behavior' => $behavior]);
    }


    public function update(Request $request)
    {//
        //dd(1);
        $data =  $this->validate($request, [
            'activity_name' => 'required',
            'document_number' => 'required'
        ]);

        $imagePath = $request->resources ?? null;

        if ($request->hasFile('resources')) {
            $image = $request->file('resources');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $document = PersonalDocumentaryFile::findOrFail($request->id);
        if (!$document) {
            return response()->json(['message' => 'Documentacion Personal no encontrado'], 404);
        }
        $document->update([
            'management_document_type_id'   => intval($request->management_document_type_id),
            'activity_name'                 => $request->activity_name,
            'document_number'               => $request->document_number,
            'description'                   => $request->description,
            'observations'                  => $request->observations,
            'fecha'                         => $request->fecha,
            'resources'                     => $imagePath,
            'cv_bank_id'                    => $request->cv_bank_id
        ]);

        return response()->json(['message' => 'Documentacion Personal actualizado correctamente', 'data' => $document], 200);
    }


    public function destroy($id)
    {
        $document = PersonalDocumentaryFile::find($id);

        if (!$document) {
            return response()->json(['message' => 'Documentacion Personal no encontrado'], 404);
        }

        $document->delete();

        return response()->json(['message' => 'Documentacion Personal eliminado correctamente'], 204);
    }
}
