<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfertasEmpleo extends Model
{
    use HasFactory;
    
    protected $table = 'ofertas_empleo';

    protected $fillable = [
        'estado', 
        'empresa', 
        'telefono', 
        'nombre_puesto', 
        'requisitos',
        'domain_id'
    ];

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}
