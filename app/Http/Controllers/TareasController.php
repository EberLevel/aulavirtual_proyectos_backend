<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
class TareasController extends Controller
{
    /**
     * Listar todas las tareas
     * GET /api/tareas
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Tarea::query();

            // Filtros opcionales
            if ($request->has('name')) {
                $query->byName($request->name);
            }

            if ($request->has('date')) {
                $query->byDate($request->date);
            }

            // Ordenamiento
            $orderBy = $request->get('order_by', 'created_at');
            $orderDirection = $request->get('order_direction', 'desc');
            $query->orderBy($orderBy, $orderDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $tareas = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $tareas->items(),
                'pagination' => [
                    'current_page' => $tareas->currentPage(),
                    'last_page' => $tareas->lastPage(),
                    'per_page' => $tareas->perPage(),
                    'total' => $tareas->total(),
                    'from' => $tareas->firstItem(),
                    'to' => $tareas->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener las tareas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva tarea
     * POST /api/tareas
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $tarea = Tarea::create([
                'name'=> $request->name,
                'descripcion' => $request->descripcion,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tarea creada exitosamente',
                'data' => $tarea
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una tarea específica
     * GET /api/tareas/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $tarea = Tarea::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $tarea
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tarea no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener la tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una tarea
     * PUT /api/tareas/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $tarea = Tarea::findOrFail($id);
            $tarea->update([
                'name' => $request->name,
                'descripcion' => $request->descripcion,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tarea actualizada exitosamente',
                'data' => $tarea->fresh()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tarea no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar la tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una tarea
     * DELETE /api/tareas/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $tarea = Tarea::findOrFail($id);
            $tarea->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Tarea eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tarea no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar la tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Búsqueda avanzada de tareas
     * POST /api/tareas/search
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = Tarea::query();

            // Búsqueda por nombre
            if ($request->has('name') && !empty($request->name)) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            // Búsqueda por descripción
            if ($request->has('descripcion') && !empty($request->descripcion)) {
                $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
            }

            // Filtro por rango de fechas
            if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
                $query->whereBetween('created_at', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);
            }

            $tareas = $query->recent()->paginate($request->get('per_page', 15));

            return response()->json([
                'status' => 'success',
                'data' => $tareas->items(),
                'pagination' => [
                    'current_page' => $tareas->currentPage(),
                    'last_page' => $tareas->lastPage(),
                    'per_page' => $tareas->perPage(),
                    'total' => $tareas->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de tareas
     * GET /api/tareas/stats
     */
    public function stats(): JsonResponse
    {
        try {
            $total = Tarea::count();
            $hoy = Tarea::whereDate('created_at', Carbon::today())->count();
            $estaSemana = Tarea::whereBetween('created_at', [
                 Carbon::now()->startOfWeek(),
                 Carbon::now()->endOfWeek()
            ])->count();
            $esteMes = Tarea::whereMonth('created_at', Carbon::now()->month)
                           ->whereYear('created_at', Carbon::now()->year)
                           ->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total' => $total,
                    'creadas_hoy' => $hoy,
                    'creadas_esta_semana' => $estaSemana,
                    'creadas_este_mes' => $esteMes,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminación múltiple
     * DELETE /api/tareas/bulk
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:tareas,id'
            ]);

            $deletedCount = Tarea::whereIn('id', $request->ids)->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Se eliminaron {$deletedCount} tareas exitosamente",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar las tareas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
