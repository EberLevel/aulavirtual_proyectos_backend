<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacionAcademicaCandidato extends Model
{
    use HasFactory;

    // Nombre de la tabla asociada al modelo
    protected $table = 'informacion_academica_candidato';

    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'avance',
        'estado_id',
        'nombre',
        'observaciones',
        'certificado',
        'domain_id',
        'candidato_id',
    ];

    // Los campos que deberían ser tratados como fechas
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Relación con el modelo `Candidato`
     */
    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    /**
     * Relación con el modelo `EstadoAvance`
     */
    public function estadoAvance()
    {
        return $this->belongsTo(EstadoAvance::class, 'estado_id');
    }

    /**
     * Relación con el modelo `Domain`
     */
    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}
