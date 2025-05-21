<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ciclo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ciclos';

    protected $fillable = [
        'nombre',
        'color',
        'domain_id',
        'orden'
    ];
}