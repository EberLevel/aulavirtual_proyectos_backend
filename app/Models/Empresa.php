<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    protected $table="companies";

    public $timestamps = true;

    protected $fillable = [
        'id', 'name','domain','database','status','rol_id','domain_id'
    ];
}
