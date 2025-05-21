<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoGestion extends Model
{
    protected $table = 'documento_gestion';
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'costo',
        'recursos',
        'estado',
        'domain_id',
        'user_id'
    ];
}
