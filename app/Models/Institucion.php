<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';

    protected $fillable = [
        'codigo',
        'nivel',
        'siglas',
        'nombre',
        'ubigeo',
        'direccion',
        'telefono',
        'domain_id',
        "institucionPadre"
    ];

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}
