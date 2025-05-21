<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VinculoLaboral extends Model
{
    use HasFactory;
    protected $table = 'vinculo_laboral';

    protected $fillable = [

        'nombre',
        'domain_id'
    ];
}
