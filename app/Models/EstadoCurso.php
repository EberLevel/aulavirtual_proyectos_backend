<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoCurso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plan_de_estudios';

    protected $fillable = [
        'nombre',
        'color',
        'domain_id',
    ];
}