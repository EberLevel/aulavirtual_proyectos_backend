<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelCargo extends Model
{
    protected $table = 'nivel_puesto'; 

    protected $fillable = ['nombre', 'domain_id'];
}
