<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domains extends Model
{
    protected $table = 'domains';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'domain_id',
    ];

}
