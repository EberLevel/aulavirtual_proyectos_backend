<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucione extends Model
{
    protected  $fillable = ['nombre','siglas', 'logotipo', 'dominio'];
}
