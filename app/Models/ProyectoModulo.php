<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoModulo extends Model
{
    use HasFactory;
    
    protected $table = 'proyecto_modulo';

    protected $fillable = [
        'nombre', 
        'prioridad',
        'estado',
        'grupo',
        'responsable',
        'descripcion',
        'proyecto_id'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function tareas()
    {
        return $this->hasMany(ProyectoTarea::class, 'proyecto_modulo_id');
    }
}
