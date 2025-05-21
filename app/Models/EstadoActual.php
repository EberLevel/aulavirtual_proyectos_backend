<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoActual extends Model
{
    use HasFactory;
    protected $table = 'estado_actual';

    protected $fillable = [

        'nombre','domain_id',

    ];
}
