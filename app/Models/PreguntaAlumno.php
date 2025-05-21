<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaAlumno extends Model
{
    protected $table = 'pregunta_alumno';

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id'); // Relación con la tabla 'alumnos'
    }

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id'); // Relación con la tabla 'preguntas'
    }
}
