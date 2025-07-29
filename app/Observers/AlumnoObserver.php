<?php

namespace App\Observers;

use App\Models\Alumno;
use App\Models\Curso;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AlumnoObserver
{
    /**
     * Handle the Alumno "created" event.
     */
    public function created(Alumno $alumno): void
    {
        $this->syncCursosParaAlumno($alumno);
    }

    /**
     * Handle the Alumno "updated" event.
     */
    public function updated(Alumno $alumno): void
    {
        // Solo re-sincronizar si cambió la carrera o el plan de estudios
        if ($alumno->wasChanged(['carrera_id', 'estado_id'])) {
            $this->syncCursosParaAlumno($alumno, true);
        }
    }

    /**
     * Sincronizar cursos para un alumno específico
     */
    private function syncCursosParaAlumno(Alumno $alumno, bool $esActualizacion = false): void
    {
        try {
            // Si es actualización, eliminar enlaces existentes
            if ($esActualizacion) {
                DB::table('curso_alumno')
                    ->where('alumno_id', $alumno->id)
                    ->where('domain_id', $alumno->domain_id)
                    ->delete();
            }

            // Buscar cursos que coincidan con:
            // - misma carrera_id
            // - mismo estado_id (plan de estudios)
            // - mismo domain_id
            $cursos = DB::table('cursos')
                ->where('carrera_id', $alumno->carrera_id)
                ->where('estado_id', $alumno->estado_id) // Esta es la conexión con plan_de_estudios
                ->where('domain_id', $alumno->domain_id)
                ->whereNull('deleted_at')
                ->get();

            // Crear enlaces en curso_alumno
            foreach ($cursos as $curso) {
                // Verificar que no exista ya la relación
                $existeRelacion = DB::table('curso_alumno')
                    ->where('curso_id', $curso->id)
                    ->where('alumno_id', $alumno->id)
                    ->where('domain_id', $alumno->domain_id)
                    ->exists();

                if (!$existeRelacion) {
                    DB::table('curso_alumno')->insert([
                        'curso_id' => $curso->id,
                        'alumno_id' => $alumno->id,
                        'domain_id' => $alumno->domain_id,
                        'estado_id' => 1, // Estado por defecto (activo)
                        'estado_curso_id' => 1, // Estado curso por defecto
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            Log::info("Alumno {$alumno->id} enlazado con " . count($cursos) . " cursos del plan de estudios {$alumno->estado_id}");

        } catch (\Exception $e) {
            Log::error("Error al sincronizar cursos para alumno {$alumno->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Alumno "deleted" event.
     */
    public function deleted(Alumno $alumno): void
    {
        // Eliminar todos los enlaces cuando se elimina un alumno
        DB::table('curso_alumno')
            ->where('alumno_id', $alumno->id)
            ->delete();
    }
}