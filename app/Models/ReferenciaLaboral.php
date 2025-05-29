<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CvBank;

class ReferenciaLaboral extends Model
{
    use HasFactory;

    protected $table = 'referencias_laborales';

    protected $fillable = [
        'nombre',
        'celular',
        'ocupacion',
        'id_postulante',  // Relación con el postulante
        'domain_id',      // Relación con el dominio si es necesario
    ];

    // Relación con la tabla 'cv_banks' (postulantes)
    public function postulante()
    {
        return $this->belongsTo(CvBank::class, 'id_postulante');
    }

    // Relación con la tabla 'domains'
    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}
