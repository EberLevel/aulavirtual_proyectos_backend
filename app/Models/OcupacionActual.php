<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcupacionActual extends Model
{
    use HasFactory;
    protected $table = 'ocupacion_actual';

    protected $fillable = [

        'nombre',
        'domain_id'
    ];
}
