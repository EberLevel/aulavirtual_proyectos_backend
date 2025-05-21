<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    use HasFactory;
    protected $table = 'estado_civil';

    protected $fillable = [

        'nombre','domain_id'

    ];

    public function domain() {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}
