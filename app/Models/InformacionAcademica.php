<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacionAcademica extends Model
{
    use HasFactory;

    protected $table = 'informacion_academica';

    protected $fillable = [
        'grado_instruccion_id',
        'profesion_id',
        'estado_avance_id',
        'domain_id',
        'id_postulante',
        'institucion',
        'fecha_inicio',
        'fecha_termino',
        'observaciones',
        'imagen_certificado',
        'nro_pagina_cv',
        'validado'
    ];

    /**
     * Relaciones con otras tablas
     */

    // Relación con el modelo GradoInstruccion
    public function gradoInstruccion()
    {
        return $this->belongsTo(GradoInstruccion::class, 'grado_instruccion_id');
    }

    // Relación con el modelo Profesion
    public function profesion()
    {
        return $this->belongsTo(Profesion::class, 'profesion_id');
    }

    // Relación con el modelo EstadoAvance
    public function estadoAvance()
    {
        return $this->belongsTo(EstadoAvance::class, 'estado_avance_id');
    }
}
