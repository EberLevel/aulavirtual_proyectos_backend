<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradoInstruccion extends Model
{
    use HasFactory;
    protected $table = 'grado_instruccion';

    protected $fillable = [

        'nombre',
        'nivel',
        'porcentaje',
        'domain_id'

    ];

    public function domain() {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}
