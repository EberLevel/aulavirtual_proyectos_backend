<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accepted extends Model
{
    use HasFactory;
    protected $table = 'accepteds';

    protected $fillable = [
        'description'
    ];
}
