<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carreras';

    protected $fillable = ['codigo', 'nombres', 'domain_id', 'plan_de_estudios_id'];

    public function planDeEstudios()
    {
        return $this->belongsTo(EstadoCurso::class, 'plan_de_estudios_id');
    }
}