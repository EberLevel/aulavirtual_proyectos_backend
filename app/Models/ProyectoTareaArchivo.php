<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoTareaArchivo extends Model
{
    use HasFactory;
    
    protected $table = 'proyecto_tarea_archivos';

    protected $fillable = [
        'contenido',
        'proyecto_tarea_id'
    ];

    public function proyecto_tarea()
    {
        return $this->belongsTo(ProyectoTarea::class, 'proyecto_tarea_id');
    }
}
