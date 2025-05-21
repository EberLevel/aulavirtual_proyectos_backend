<?php

namespace App\Http\Controllers;

use App\Models\Evaluaciones;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EvaluacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $disk = "public";

    public function index($id)
    {
        $evaluaciones = Evaluaciones::leftJoin('estados', 'estados.id', '=', 'evaluaciones.estado_id')
            ->leftJoin('t_g_parametros as tipo_evaluacion', 'tipo_evaluacion.nu_id_parametro', '=', 'evaluaciones.tipo_evaluacion_id')
            ->where('evaluaciones.grupo_de_evaluaciones_id', $id)
            ->whereNull('evaluaciones.deleted_at')
            ->select(
                'evaluaciones.*',
                'estados.nombre as estado_nombre',
                'tipo_evaluacion.tx_item_description as tipo_evaluacion_nombre'
            )
            ->get();

        return response()->json($evaluaciones);
    }

    public function getEvaluacionById($id)
    {
        // Buscar la evaluación por su ID
        $evaluacion = Evaluaciones::select(
            'id',
            'nombre',
            'tipo_evaluacion_id',
            'fecha_y_hora_programo',
            'observaciones',
            'estado_id',
            'domain_id',
            'porcentaje_asignado',
            'grupo_de_evaluaciones_id',
            'modalidad',
            'contenido',
            'texto_enrriquesido'
        )
            ->where('id', $id)
            ->first();

        // Verificar si se encontró la evaluación
        if (!$evaluacion) {
            return response()->json(['message' => 'Evaluación no encontrada'], 404);
        }

        // Devolver los datos de la evaluación
        return response()->json($evaluacion);
    }

    public function updateEvaluacionById(Request $request, $id)
    {
        $validatedData = $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'tipo_evaluacion_id' => 'nullable|exists:t_g_parametros,nu_id_parametro',
            'porcentaje_evaluacion' => 'nullable|numeric',
            'porcentaje_asignado' => 'nullable|numeric', // Validamos el nuevo campo
            'fecha_y_hora_programo' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required|exists:estados,id',
            'domain_id' => 'required|integer',
            'grupo_de_evaluaciones_id' => 'required|integer',
            'modalidad' => 'required|in:0,1',
            'recursos' => 'array', // Puede ser un array de archivos
            'recursos.*' => 'file|max:10240',
            'texto_enrriquesido' => 'nullable|string'
        ]);



        $evaluacion = Evaluaciones::find($id);

        if (!$evaluacion) {
            return response()->json(['message' => 'Evaluación no encontrada'], 404);
        }

        $evaluacion->update($validatedData);

        $fileUrls = [];
        if ($request->hasFile('recursos')) {
            foreach ($request->file('recursos') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/uploads', $fileName);
                $fileUrls[] = url('storage/' . str_replace('public/', '', $path));
            }
        }

        // Guardamos URLs de archivos si existen
        if (!empty($fileUrls)) {
            $evaluacion->contenido = json_encode($fileUrls);
        }

        $evaluacion->save();

        return response()->json($evaluacion);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        return $request->input('texto_enrriquesido');
        $validatedData = $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'tipo_evaluacion_id' => 'nullable|exists:t_g_parametros,nu_id_parametro',
            'porcentaje_evaluacion' => 'nullable|numeric',
            'porcentaje_asignado' => 'nullable|numeric', // Validamos el nuevo campo
            'fecha_y_hora_programo' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required',
            'domain_id' => 'required|integer',
            'grupo_de_evaluaciones_id' => 'required|integer',
            'modalidad' => 'required|in:0,1',
            'recursos' => 'array', // Puede ser un array de archivos
            'recursos.*' => 'file|max:10240'
        ]);

        $validatedData['fecha_y_hora_programo'] = Carbon::parse($validatedData['fecha_y_hora_programo'])->format('Y-m-d H:i:s');

        $evaluacion = Evaluaciones::create($validatedData);

        $fileUrls = [];

        // dd($request);
        foreach ($request->file('recursos') as $file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/uploads', $fileName);
            $fileUrls[] = url('storage/' . str_replace('public/', '', $path));
        }

        // Actualizar el examen con las URLs de los archivos
        $evaluacion->contenido = json_encode($fileUrls);
        $evaluacion->save();

        return response()->json([
            'message' => 'Examen guardado exitosamente con los archivos',
            'fileUrls' => $fileUrls,
            "evaluacion" => $evaluacion
        ], 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Evaluaciones  $Evaluaciones
     * @return \Illuminate\Http\Response
     */
    public function show(Evaluaciones $Evaluaciones)
    {
        return response()->json($Evaluaciones);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evaluaciones  $Evaluaciones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evaluaciones $Evaluaciones)
    {
        $validatedData = $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'tipo_evaluacion_id' => 'nullable|exists:t_g_parametros,nu_id_parametro',
            'fecha_y_hora_programo' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required',
            'porcentaje_asignado' => 'nullable|numeric',
            'domain_id' => 'required|integer',
            'grupo_de_evaluaciones_id' => 'required|integer',
            'modalidad' => 'required|in:0,1'
        ]);

        $Evaluaciones->update($validatedData);
        return response()->json($Evaluaciones);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Evaluaciones  $Evaluaciones
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $grupo = Evaluaciones::withTrashed()->findOrFail($id);
        $grupo->forceDelete();

        return response()->json(['message' => 'Curso eliminado exitosamente'], 201);
    }

    public function getNotasPorAlumnoYGrupo($alumnoId, $grupoId)
    {
        try {
            // Modifica la consulta para incluir el nombre del estado
            $notas = DB::table('evaluaciones_alumno as ea')
                ->join('evaluaciones as e', 'ea.evaluacion_id', '=', 'e.id')
                ->leftJoin('estados as es', 'e.estado_id', '=', 'es.id') // Hacemos el JOIN con la tabla estados
                ->where('ea.alumno_id', $alumnoId)
                ->where('e.grupo_de_evaluaciones_id', $grupoId)
                ->select(
                    'ea.id as evaluacion_alumno_id', // ID de la tabla evaluaciones_alumno
                    'ea.nota',
                    'e.id as evaluacion_id',
                    'e.nombre',
                    'e.porcentaje_asignado',
                    'e.porcentaje_evaluacion',
                    'e.fecha_y_hora_programo',
                    'e.fecha_y_hora_realizo',
                    'e.observaciones',
                    'e.modalidad',
                    'e.contenido',
                    'ea.asistencia',
                    'es.nombre as estado_nombre', // Traemos el nombre del estado desde la tabla estados
                    'e.tipo_evaluacion_id'
                )
                ->get();

            return response()->json([
                'success' => true,
                'notas' => $notas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las notas del alumno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPromedioPorAlumnoYGrupo($alumnoId, $grupoId)
    {
        try {
            $promedios = DB::table('evaluaciones_alumno as ea')
                ->join('evaluaciones as e', 'ea.evaluacion_id', '=', 'e.id')
                ->where('ea.alumno_id', $alumnoId)
                ->where('e.grupo_de_evaluaciones_id', $grupoId)
                ->select(
                    'ea.alumno_id',
                    DB::raw('AVG(ea.nota) as promedio_por_alumno')
                )
                ->groupBy('ea.alumno_id')
                ->first();

            return response()->json([
                'success' => true,
                'promedio' => $promedios->promedio_por_alumno ?? null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el promedio de las notas del alumno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEvaluacionesPorGrupo($grupoId, $alumnoId)
    {
        try {
            $evaluaciones = DB::table('evaluaciones_alumno as ea')
                ->join('evaluaciones as e', 'ea.evaluacion_id', '=', 'e.id')
                ->where('e.grupo_de_evaluaciones_id', $grupoId)
                ->where('ea.alumno_id', $alumnoId)
                ->select(
                    'ea.nota',
                    'e.nombre',
                    'e.porcentaje_asignado',
                    DB::raw('(ea.nota * e.porcentaje_asignado / 100) as nota_porcentual')  // Aquí calculamos la nota porcentual
                )
                ->get();

            return response()->json([
                'success' => true,
                'evaluaciones' => $evaluaciones,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las evaluaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    public function subirRecursos(Request $request, $evaluacion_id)
    {
        // dd($request, $evaluacion_id);

        $this->validate($request, [
            'recursos' => 'required|array',
            'recursos.*' => 'file|mimes:jpg,png,pdf,mp4|max:10240' // Aceptar solo imágenes, PDFs y videos
        ]);

        $evaluacionId = $evaluacion_id;

        // Crear un array para guardar las rutas de los archivos
        $rutas = [];

        // Subir cada archivo y guardar su ruta
        foreach ($request->file('recursos') as $file) {
            // Generar un nombre único para cada archivo
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();  // Usar UUID para un nombre único

            // Guardar el archivo en la carpeta 'uploads' dentro de 'public' con el nombre único
            $path = $file->storeAs('uploads', $filename, $this->disk);  // Guardar en public/uploads

            // Agregar la ruta del archivo al array
            $rutas[] = $path;
        }

        // Convertir el array de rutas a formato JSON
        $evaluacion = Evaluaciones::find($evaluacionId);
        $evaluacionesJson = json_encode($rutas);

        if ($evaluacion) {
            // Actualizar el campo 'contenido' de la evaluación con las nuevas rutas
            $evaluacion->contenido = $evaluacionesJson;  // Guardar las rutas en el campo 'contenido'
            $evaluacion->save();

            return response()->json(['message' => 'Archivos subidos correctamente.']);
        } else {
            return response()->json(['error' => 'Evaluación no encontrada.'], 404);
        }
    }
}
