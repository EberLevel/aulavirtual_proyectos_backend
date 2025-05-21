<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    use HasFactory;
    protected $table = 'escala';

    protected $fillable = [

        'nombre',
        'c',
        'color',
        'domain_id'
    ];
}
