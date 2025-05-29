<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CvBank;

class FormularioFinalPostulante extends Model
{
    use HasFactory;

    protected $table = 'formularioFinalPostulante';

    protected $fillable = [
        'observaciones',
        'estado_actual_id',
        'aceptacion_id',
        'nivel_cargo_final_id',
        'puntaje_id',
        'institucion',
        'tabla_referencia',
        'postulante_id'
    ];

    // Relación con la tabla ocupacion_actual
    public function estadoActual()
    {
        return $this->belongsTo(OcupacionActual::class, 'estado_actual_id');
    }

    // Relación con la tabla escala para el campo aceptacion
    public function aceptacion()
    {
        return $this->belongsTo(Escala::class, 'aceptacion_id');
    }

    // Relación con la tabla nivel_puesto
    public function nivelCargoFinal()
    {
        return $this->belongsTo(NivelCargo::class, 'nivel_cargo_final_id');
    }

    // Relación con la tabla escala para el campo puntaje
    public function puntaje()
    {
        return $this->belongsTo(Escala::class, 'puntaje_id');
    }

    // Relación con la tabla cv_banks (postulantes)
    public function postulante()
    {
        return $this->belongsTo(CvBank::class, 'postulante_id');
    }
}
