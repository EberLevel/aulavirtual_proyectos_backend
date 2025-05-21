<?php

namespace App\Http\Controllers;

use App\Models\Maintenance\ManagementDocumentType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ManagementDocumentTypeController extends Controller
{

    public function index(Request $request)
    {
        $document_type = ManagementDocumentType::query();

        if ($request->has('name')) {
            $name           = $request->input('name');
            $document_type  = $document_type->where('name', 'like', '%' . $name . '%');
        }

        $document_type = $document_type->paginate(10);

        return response()->json($document_type, 200);
    }

    public function store(Request $request)
    {

        try {
            $this->validate($request, [
                'name' => 'required',
            ]);

            $document_type = ManagementDocumentType::create([
                'name'  => $request->name,
            ]);
        } catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['document_type' => $document_type]);
    }

    public function update(Request $request, $id)
    {

        try {
            $this->validate($request, [
                'name' => 'required',
            ]);

            ManagementDocumentType::where('id', $id)->update([
                'name'  => $request->name,
            ]);

            $document_type = ManagementDocumentType::find($id);
        } catch (ValidationException | ModelNotFoundException | Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['message' => 'Tipo de Documento de Gestión actualizado correctamente', 'data' => $document_type], 200);
    }

    public function destroy($id)
    {

        $document_type = ManagementDocumentType::find($id);

        if (!$document_type) {
            return response()->json(['message' => 'Tipo de Documento de Gestión no encontrado'], 404);
        }

        $document_type->delete();

        return response()->json(['message' => 'Tipo de Documento de Gestión eliminado correctamente'], 204);
    }

    public function list(Request $request)
    {

        return response()->json(ManagementDocumentType::get(), 200);
    }
}
