<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CvBank;

class ReferenciaFamiliar extends Model
{
    use HasFactory;

    protected $table = 'referencias_familiares';

    protected $fillable = [
        'nombre',
        'celular',
        'ocupacion',
        'id_postulante',
        'domain_id',
    ];

    public function postulante()
    {
        return $this->belongsTo(CvBank::class, 'id_postulante');
    }

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}
