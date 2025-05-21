<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pregunta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'preguntas';

    protected $fillable = [
        'pregunta_docente',
        'evaluacion_id',
        'alternativas',
        'respuesta_correcta',
        'valor_pregunta',
        'url',
        'domain_id', // Si estás usando addDomainId() para agregar esta columna
        'tipo_de_evaluacion_id'
    ];

    protected $casts = [
        'alternativas' => 'array',
        'valor_pregunta' => 'decimal:2',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    
    public function preguntaAlumnos()
    {
        return $this->hasMany(PreguntaAlumno::class, 'pregunta_id');
    }
}