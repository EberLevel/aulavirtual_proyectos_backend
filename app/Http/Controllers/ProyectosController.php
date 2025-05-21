<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoModulo;
use App\Models\ProyectoTarea;
use Illuminate\Http\Request;

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

    // Crear un nuevo proyecto
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $this->validate($request, [
            'estado' => 'required|string|max:20',
            'nombre' => 'required|string|max:191',
        ]);

        // Crear un nuevo proyecto con los datos proporcionados y el domain_id
        $proyecto = Proyecto::create(array_merge($request->all(), ['domain_id' => $this->domain_id]));

        return response()->json([
            'message' => 'Proyecto creado correctamente',
            'data' => $proyecto,
        ], 201);
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

    // Actualizar un proyecto existente
    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $this->validate($request, [
            'estado' => 'required|string|max:20',
            'nombre' => 'required|string|max:191',
        ]);

        $proyecto = Proyecto::find($id);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        // Actualizar el proyecto con los nuevos datos
        $proyecto->update($request->all());

        return response()->json([
            'message' => 'Proyecto actualizado correctamente',
            'data' => $proyecto,
        ], 200);
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
        $modulos = $proyecto->modulos()->orderBy('prioridad', 'asc')->get();

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
            'decripcion' => 'nulable|string'
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

        $tareasConImagenes = $tareas->map(function ($tarea) {
            $tieneImagenes = $tarea->archivos()->exists(); // Cambia "imagenes" por "archivos"
            return [
                'id' => $tarea->id,
                'descripcion' => $tarea->descripcion,
                'prioridad' => $tarea->prioridad,
                'estado' => $tarea->estado,
                'grupo' => $tarea->grupo,
                'responsable' => $tarea->responsable,
                'hasImage' => $tieneImagenes,
            ];
        });

        return response()->json(['data' => $tareasConImagenes], 200);
    }


    // Añadir una tarea a un proyecto
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
    }


    // Actualizar una tarea de un proyecto
    public function actualizarTarea(Request $request, $proyectoId, $moduloId, $tareaId)
    {
        $this->validate($request, [
            'nombre' => 'sometimes|string|max:191',
            'prioridad' => 'sometimes|required|string|max:20',
            'estado' => 'sometimes|required|string|max:20',
            'grupo' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:50',
            'decripcion' => 'nulable|string',
            'archivos' => 'array',  // Validar que archivos es un arreglo
            'archivos.*' => 'required|string',  // Cada archivo debe ser un string base64
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
    }

    public function actualizarModulo(Request $request, $proyectoId, $moduloId)
    {
        $this->validate($request, [
            'nombre' => 'sometimes|string|max:191',
            'prioridad' => 'sometimes|required|string|max:20',
            'estado' => 'sometimes|required|string|max:20',
            'grupo' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:50',
            'decripcion' => 'nulable|string'
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
            ->with('archivos')  // Cargar los archivos relacionados
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
}
