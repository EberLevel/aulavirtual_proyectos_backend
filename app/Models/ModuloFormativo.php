<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuloFormativo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modulos_formativos';

    protected $fillable = [
        'nombre',
        'color',
        'domain_id',
    ];
}