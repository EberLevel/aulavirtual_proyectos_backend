<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleador extends Model
{
    protected $table = "empleadores";

    protected $fillable = [
        'name', 'status', 'user_id', 'domain_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domains::class);
    }
}