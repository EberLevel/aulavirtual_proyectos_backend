<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capacitacion extends Model
{
    protected $table = 'capacitaciones';
    protected $fillable = [
        /*'codigo', 'nombre', 'horas', 'sylabus', 'temas', 'idEstado', 'docente', 'fecha', 'estado'*/
        'cv_bank_id',
        'uuid',
        'name',
        'user_id',
        'status',
        'advance',
        'image',
        'insitution',
        'date_start',
        'date_end',
        'time',
        'type',
        'level_position',
        'score',
        'observation',
        'validated',
        'domain_id'
    ];


    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? url("storage/$this->image") : null;
    }

public function users()
{
    return $this->belongsTo(User::class, 'user_id');
}


public function advanceStatus()
{
    return $this->belongsTo(EstadoAvance::class, 'advance');
}


public function typeCapacitation()
{
    return $this->belongsTo(TipoCapacitacion::class, 'type');
}

public function positionLevel()
{
    return $this->belongsTo(NivelCargo::class, 'level_position');
}

public function status(){
    return $this->belongsTo(EstadoActual::class, 'status');
}

public function domain() {
    return $this->belongsTo(Domains::class, 'domain_id');
}
}


