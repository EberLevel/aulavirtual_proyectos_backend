<?php

namespace App\Observers;

use App\Models\Curso;
use App\Models\Alumno;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CursoObserver
{
    /**
     * Handle the Curso "created" event.
     */
    public function created(Curso $curso): void
    {
        $this->syncAlumnosParaCurso($curso);
    }

    /**
     * Handle the Curso "updated" event.
     */
    public function updated(Curso $curso): void
    {
        // Solo re-sincronizar si cambió la carrera o el plan de estudios
        if ($curso->wasChanged(['carrera_id', 'estado_id'])) {
            $this->syncAlumnosParaCurso($curso, true);
        }
    }

    /**
     * Sincronizar alumnos para un curso específico
     */
    private function syncAlumnosParaCurso(Curso $curso, bool $esActualizacion = false): void
    {
        try {
            // Si es actualización, eliminar enlaces existentes
            if ($esActualizacion) {
                DB::table('curso_alumno')
                    ->where('curso_id', $curso->id)
                    ->where('domain_id', $curso->domain_id)
                    ->delete();
            }

            // Buscar alumnos que coincidan con:
            // - misma carrera_id
            // - mismo estado_id (plan de estudios)
            // - mismo domain_id
            // - que estén activos (no eliminados)
            $alumnos = DB::table('alumnos')
                ->where('carrera_id', $curso->carrera_id)
                ->where('estado_id', $curso->estado_id) // Esta es la conexión con plan_de_estudios
                ->where('domain_id', $curso->domain_id)
                ->whereNull('deleted_at')
                ->get();

            // Crear enlaces en curso_alumno
            foreach ($alumnos as $alumno) {
                // Verificar que no exista ya la relación
                $existeRelacion = DB::table('curso_alumno')
                    ->where('curso_id', $curso->id)
                    ->where('alumno_id', $alumno->id)
                    ->where('domain_id', $curso->domain_id)
                    ->exists();

                if (!$existeRelacion) {
                    DB::table('curso_alumno')->insert([
                        'curso_id' => $curso->id,
                        'alumno_id' => $alumno->id,
                        'domain_id' => $curso->domain_id,
                        'estado_id' => 1, // Estado por defecto (activo)
                        'estado_curso_id' => 1, // Estado curso por defecto
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            Log::info("Curso {$curso->id} enlazado con " . count($alumnos) . " alumnos del plan de estudios {$curso->estado_id}");

        } catch (\Exception $e) {
            Log::error("Error al sincronizar alumnos para curso {$curso->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Curso "deleted" event.
     */
    public function deleted(Curso $curso): void
    {
        // Eliminar todos los enlaces cuando se elimina un curso
        DB::table('curso_alumno')
            ->where('curso_id', $curso->id)
            ->delete();
    }
}