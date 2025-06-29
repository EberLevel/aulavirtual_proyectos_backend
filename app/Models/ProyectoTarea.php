<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoTarea extends Model
{
    use HasFactory;

    protected $table = 'proyecto_tarea';

    protected $fillable = [
        'nombre',
        'prioridad',
        'estado',
        'grupo',
        'responsable',
        'descripcion',
        'proyecto_modulo_id',
        'observaciones',
        'respuesta_proveedor',
        'respuesta_cliente',
    ];

    public function modulo()
    {
        return $this->belongsTo(ProyectoModulo::class, 'proyecto_modulo_id');
    }

    public function archivos()
    {
        return $this->hasMany(ProyectoTareaArchivo::class, 'proyecto_tarea_id');
    }
}
