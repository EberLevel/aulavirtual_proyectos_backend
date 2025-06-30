<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoModulo;
use App\Models\ProyectoTarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProyectosController extends Controller
{
    protected $domain_id;

    public function __construct(Request $request)
    {
        // Asignar el domain_id desde los atributos de la solicitud
        $this->domain_id = $request->attributes->get('domain_id');
    }

    // Listar todos los proyectos filtrados por domain_id
    public function index($domain_id)
    {
        $proyectos = Proyecto::where('domain_id', $domain_id)->paginate(10);
        return response()->json([
            'data' => $proyectos->items(),
            'pagination' => [
                'total' => $proyectos->total(),
                'per_page' => $proyectos->perPage(),
                'current_page' => $proyectos->currentPage(),
                'last_page' => $proyectos->lastPage(),
                'from' => $proyectos->firstItem(),
                'to' => $proyectos->lastItem(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'estado' => 'required|string|max:20',
            'nombre' => 'required|string|max:191',
            'domain_id' => 'required|integer|exists:domains,id', // Validate domain_id
        ]);

        $proyecto = Proyecto::create($request->all());
        return response()->json([
            'message' => 'Proyecto creado correctamente',
            'data' => $proyecto,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'estado' => 'required|string|max:20',
            'nombre' => 'required|string|max:191',
            'domain_id' => 'required|integer|exists:domains,id', // Validate domain_id
        ]);

        $proyecto = Proyecto::find($id);
        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        $proyecto->update($request->all());
        return response()->json([
            'message' => 'Proyecto actualizado correctamente',
            'data' => $proyecto,
        ], 200);
    }

    // Mostrar un proyecto específico por ID
    public function show($id)
    {
        $proyecto = Proyecto::find($id);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        return response()->json(['data' => $proyecto], 200);
    }

    // Eliminar un proyecto
    public function destroy($id)
    {
        $proyecto = Proyecto::find($id);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        // Eliminar el proyecto
        $proyecto->delete();

        return response()->json(['message' => 'Proyecto eliminado correctamente'], 204);
    }

    // listar modulos de un proyecto
    public function listarModulos($proyectoId)
    {
        $proyecto = Proyecto::find($proyectoId);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        // Ordenar las tareas por el campo 'prioridad' de manera ascendente
        $modulos = $proyecto->modulos()
        ->leftJoin('tareas', 'proyecto_modulo.tarea_id', '=', 'tareas.id')
        ->select('proyecto_modulo.*', 'tareas.name as tarea_nombre')
        ->orderBy('proyecto_modulo.prioridad', 'asc')->get();

        return response()->json(['data' => $modulos], 200);
    }

    public function anadirModulo(Request $request, $proyectoId)
    {
        $this->validate($request, [
            'nombre' => 'sometimes|string|max:191',
            'prioridad' => 'required|string|max:20',
            'estado' => 'required|string|max:20',
            'grupo' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:50',
            'decripcion' => 'nullable|string',
            'tarea_id' => 'sometimes|required',
        ]);

        $proyecto = Proyecto::find($proyectoId);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        $tarea = new ProyectoModulo(array_merge($request->all(), ['proyecto_id' => $proyecto->id]));
        $tarea->save();

        return response()->json([
            'message' => 'Módulo añadido correctamente al proyecto',
            'data' => $tarea,
            'proyecto' => $proyecto->id
        ], 201);
    }

    // Listar las tareas de un proyecto específico
    public function listarTareas($proyectoId, $moduloId)
    {
        $modulo = ProyectoModulo::find($moduloId);

        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        $tareas = $modulo->tareas()->orderBy('prioridad', 'asc')->get();

        $tareasConImagenes = $tareas->map(function ($tarea) use ($modulo){
            $tieneImagenes = $tarea->archivos()->exists(); // Cambia "imagenes" por "archivos"
            return [
                'id' => $tarea->id,
                'descripcion' => $tarea->descripcion,
                'prioridad' => $tarea->prioridad,
                'estado' => $tarea->estado,
                'grupo' => $tarea->grupo,
                'responsable' => $tarea->responsable,
                'hasImage' => $tieneImagenes,
                'tarea_id' => $modulo->tarea_id,
            ];
        });

        return response()->json(['data' => $tareasConImagenes], 200);
    }


    // Añadir una tarea a un proyecto
    /*
    public function anadirTarea(Request $request, $proyectoId, $moduloId)
    {
        $this->validate($request, [
            'nombre' => 'sometimes|string|max:191',
            'prioridad' => 'required|string|max:20',
            'estado' => 'required|string|max:20',
            'grupo' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:50',
            'decripcion' => 'nulable|string',
            'archivos' => 'array',  // Validar que archivos es un arreglo
            'archivos.*' => 'required|string',  // Cada elemento del array archivos debe ser un string
        ]);

        $modulo = ProyectoModulo::find($moduloId);

        if (!$modulo) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        $tarea = new ProyectoTarea(array_merge($request->all(), ['proyecto_modulo_id' => $modulo->id]));
        $tarea->save();

        // Guardar los archivos en la tabla proyecto_tarea_archivos
        if ($request->has('archivos')) {
            foreach ($request->input('archivos') as $contenido) {
                $tarea->archivos()->create([
                    'contenido' => $contenido,  // Guardar el string base64 directamente
                ]);
            }
        }

        return response()->json([
            'message' => 'Tarea añadida correctamente al modulo',
            'data' => $tarea,
            'mdoulo' => $modulo->id
        ], 201);
    }*/

    // Añadir una tarea a un proyecto
    public function anadirTarea(Request $request, $proyectoId, $moduloId)
    {
        $this->validate($request, [
            'nombre' => 'sometimes|string|max:191',
            'prioridad' => 'required|string|max:20',
            'estado' => 'required|string|max:20',
            'grupo' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'archivos' => 'array',
            'archivos.*' => 'required|string',
            'tarea_id' => 'required|integer', // Agregar validación para tarea_id
        ]);

        $modulo = ProyectoModulo::where('tarea_id', $request->input('tarea_id'))
                            ->where('proyecto_id', $proyectoId)
                            ->first();

        if (!$modulo) {
            error_log("Módulo no encontrado");
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        error_log("Módulo encontrado: " . $modulo->id);

        // Crear la tarea incluyendo el módulo_id
        $tarea = new ProyectoTarea(array_merge(
            $request->except('tarea_id'),
            ['proyecto_modulo_id' => $modulo->id]
        ));
        $tarea->save();

        // Guardar los archivos en la tabla proyecto_tarea_archivos
        if ($request->has('archivos')) {
            foreach ($request->input('archivos') as $contenido) {
                $tarea->archivos()->create([
                    'contenido' => $contenido,
                ]);
            }
        }

        return response()->json([
            'message' => 'Tarea añadida correctamente al modulo',
            'data' => $tarea,
            'modulo' => $modulo->id
        ], 201);
    }


    // Actualizar una tarea de un proyecto
    /*public function actualizarTarea(Request $request, $proyectoId, $moduloId, $tareaId)
    {
        $this->validate($request, rules: [
            'nombre' => 'sometimes|string|max:191',
            'prioridad' => 'sometimes|required|string|max:20',
            'estado' => 'sometimes|required|string|max:20',
            'grupo' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:50',
            'decripcion' => 'nulable|string',
            'archivos' => 'array',  // Validar que archivos es un arreglo
            'archivos.*' => 'required|string',  // Cada archivo debe ser un string base64
            'tarea_id'=> 'sometimes|required',  // Validar que tarea es un arreglo
        ]);

        $modulo = ProyectoModulo::find($moduloId);

        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        $tarea = ProyectoTarea::where('proyecto_modulo_id', $modulo->id)->find($tareaId);

        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        // Actualizar la tarea con los datos proporcionados
        $tarea->update($request->all());

        // Eliminar todos los archivos existentes de la tarea
        $tarea->archivos()->delete();

        // Guardar los nuevos archivos en la tabla proyecto_tarea_archivos
        if ($request->has('archivos')) {
            foreach ($request->input('archivos') as $contenido) {
                $tarea->archivos()->create([
                    'contenido' => $contenido,  // Guardar el string base64 directamente
                ]);
            }
        }

        return response()->json([
            'message' => 'Tarea actualizada correctamente',
            'data' => $tarea,
        ], 200);
    }*/

    public function actualizarTarea(Request $request, $proyectoId, $moduloId, $tareaId)
    {
        $this->validate($request, [
            'nombre' => 'sometimes|string|max:191',
            'prioridad' => 'sometimes|required|string|max:20',
            'estado' => 'sometimes|required|string|max:20',
            'grupo' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'archivos' => 'array',
            'archivos.*' => 'required|string',
            'tarea_id' => 'required|integer',
        ]);

        $modulo = ProyectoModulo::where('tarea_id', $request->input('tarea_id'))
                            ->where('proyecto_id', $proyectoId)
                            ->first();

        if (!$modulo) {
            error_log("Módulo no encontrado");
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        error_log("Módulo encontrado: " . $modulo->id);

        // Buscar la tarea directamente por su ID
        $tarea = ProyectoTarea::find($tareaId);

        if (!$tarea) {
            error_log("Tarea no encontrada con ID: " . $tareaId);
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        error_log("Tarea encontrada: " . $tarea->id);

        // Actualizar la tarea incluyendo el módulo_id
        $tarea->update(array_merge(
            $request->except('tarea_id'),
            ['proyecto_modulo_id' => $modulo->id]
        ));

        // Eliminar todos los archivos existentes de la tarea
        $tarea->archivos()->delete();

        // Guardar los nuevos archivos
        if ($request->has('archivos')) {
            foreach ($request->input('archivos') as $contenido) {
                $tarea->archivos()->create([
                    'contenido' => $contenido,
                ]);
            }
        }

        return response()->json([
            'message' => 'Tarea actualizada correctamente',
            'data' => $tarea,
        ], 200);
    }


    public function actualizarModulo(Request $request, $proyectoId, $moduloId)
    {
        $this->validate($request, [
            'nombre' => 'sometimes|string|max:191',
            'prioridad' => 'sometimes|required|string|max:20',
            'estado' => 'sometimes|required|string|max:20',
            'grupo' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:50',
            'decripcion' => 'nullable|string',
            'tarea_id' => 'sometimes|required',
        ]);

        $proyecto = Proyecto::find($proyectoId);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        $modulo = ProyectoModulo::where('proyecto_id', $proyecto->id)->find($moduloId);

        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        // Actualizar la tarea con los datos proporcionados
        $modulo->update($request->all());

        return response()->json([
            'message' => 'Modulo actualizada correctamente',
            'data' => $modulo,
        ], 200);
    }


    // Eliminar una tarea de un proyecto
    public function eliminarTarea($proyectoId, $moduloId, $tareaId)
    {
        $modulo = ProyectoModulo::find($moduloId);

        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        $tarea = ProyectoTarea::where('proyecto_modulo_id', $modulo->id)->find($tareaId);

        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        // Eliminar la tarea
        $tarea->delete();

        return response()->json(['message' => 'Tarea eliminada correctamente'], 204);
    }

    public function eliminarModulo($proyectoId, $moduloId)
    {
        $proyecto = Proyecto::find($proyectoId);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        $modulo = ProyectoModulo::where('proyecto_id', $proyecto->id)->find($moduloId);

        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrada'], 404);
        }

        // Eliminar la tarea
        $modulo->delete();

        return response()->json(['message' => 'Tarea eliminada correctamente'], 204);
    }

    // Mostrar una tarea específica por ID junto con sus archivos
    public function mostrarTarea($proyectoId, $moduloId, $tareaId)
    {
        $modulo = ProyectoModulo::find($moduloId);

        if (!$modulo) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        // Buscar la tarea específica dentro del proyecto
        $tarea = ProyectoTarea::where('proyecto_modulo_id', $modulo->id)
            ->with(['archivos','modulo'])  // Cargar los archivos relacionados
            ->find($tareaId);

        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        return response()->json(['data' => $tarea], 200);
    }

    public function mostrarModulo($proyectoId, $moduloId)
    {
        $proyecto = Proyecto::find($proyectoId);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        // Buscar la tarea específica dentro del proyecto
        $modulo = ProyectoModulo::where('proyecto_id', $proyecto->id)
            ->find($moduloId);

        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        return response()->json(['data' => $modulo], 200);
    }


    public function updateTareaEstado(Request $request, $proyectoId, $moduloId, $tareaId)
    {
        try {
            // Validar el request
            $validator = Validator::make($request->all(), [
                'estado' => 'required|string|in:PENDIENTE,EN PROCESO,OBSERVADO,REVISIÓN,RETRASADO,APROBADO'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Verificar que el proyecto existe
            $proyecto = Proyecto::findOrFail($proyectoId); // Cambié $projectId por $proyectoId

            // Verificar que el módulo pertenece al proyecto
            $modulo = ProyectoModulo::where('id', $moduloId)
                ->where('proyecto_id', $proyectoId) // Cambié $projectId por $proyectoId
                ->firstOrFail();

            // Buscar la tarea que pertenece al módulo
            $tarea = ProyectoTarea::where('id', $tareaId)
                ->where('proyecto_modulo_id', $moduloId)
                ->firstOrFail();

            // Guardar el estado anterior para log (opcional)
            $estadoAnterior = $tarea->estado;

            // Actualizar el estado
            $tarea->estado = $validated['estado'];
            $tarea->save();

            // Respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Estado de la tarea actualizado correctamente',
                'data' => [
                    'id' => $tarea->id,
                    'estado' => $tarea->estado,
                    'nombre' => $tarea->nombre,
                    'prioridad' => $tarea->prioridad,
                    'descripcion' => $tarea->descripcion,
                    'updated_at' => $tarea->updated_at->format('Y-m-d H:i:s')
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Recurso no encontrado',
                'error' => 'La tarea, módulo o proyecto especificado no existe'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => 'No se pudo actualizar el estado de la tarea',
                'debug' => $e->getMessage() // Solo para debugging, remover en producción
            ], 500);
        }
    }
}
