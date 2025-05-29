<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CvBank;
use App\Models\Ano;

class CapacitacionPostulante extends Model
{
    use HasFactory;

    protected $table = 'capacitacion_postulante';

    protected $fillable = [
        'nombre',
        'estado',
        'institucion',
        'fecha_inicio',
        'fecha_termino',
        'imagen_certificado',
        'observaciones',
         'tiempo',
        'domain_id',
        'id_postulante'
    ];

// En el modelo CapacitacionPostulante
public function estadoAno()
{
    return $this->belongsTo(Ano::class, 'estado');
}


    // Relación con la tabla 'domains'
    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

    // Relación con la tabla 'cv_banks' (postulantes)
    public function postulante()
    {
        return $this->belongsTo(CvBank::class, 'id_postulante');
    }
}
