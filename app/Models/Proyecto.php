<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;
    
    protected $table = 'proyectos';

    protected $fillable = [
        'estado',
        'nombre',
        'domain_id'
    ];

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

    public function modulos()
    {
        return $this->hasMany(ProyectoModulo::class, 'proyecto_id');
    }
}
