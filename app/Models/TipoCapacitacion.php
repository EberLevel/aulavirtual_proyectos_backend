<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCapacitacion extends Model
{
    use HasFactory;
    protected $table = 'training_types';

    protected $fillable = [

        'nombre',
        'domain_id'
    ];
}
