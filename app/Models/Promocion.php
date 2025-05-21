<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'promociones';

    // Campos que pueden ser llenados masivamente
    protected $fillable = [
        'nombre_promocion',
        'descripcion',
        'fecha_inscripcion',
        'domain_id'
    ];

    // Relación con la tabla alumnos (una promoción puede tener muchos alumnos)
    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'promocion_id', 'id');
    }

    // Relación con la tabla domains (una promoción pertenece a un dominio)
    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id', 'id');
    }
}
