<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ano extends Model
{
    protected $table = 'ano';

    protected $fillable = [
        'nombre',
        'domain_id',
    ];
}
