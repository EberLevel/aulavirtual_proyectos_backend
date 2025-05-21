<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoAlumno extends Model
{
    protected $table = 'curso_alumno'; // Nombre de la tabla

    protected $fillable = [
        'curso_id',
        'alumno_id',
        'domain_id',
        'estado_id',
    ];

    // Aquí puedes definir las relaciones si es necesario
    // Por ejemplo, si tienes relaciones con Curso y Alumno:
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
}
