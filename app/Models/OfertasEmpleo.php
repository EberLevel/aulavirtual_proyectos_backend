<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empleador;

class OfertasEmpleo extends Model
{
    use HasFactory;
    
    protected $table = 'ofertas_empleo';

    protected $fillable = [
        'estado', 
        // 'empresa', 
        'empleador_id',
        'plain_empleador',
        'telefono', 
        'nombre_puesto', 
        'url',
        'requisitos',
        'domain_id'
    ];

    public function empleador()
    {
        return $this->belongsTo(Empleador::class, 'empleador_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}
