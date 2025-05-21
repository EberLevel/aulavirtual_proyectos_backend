<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CvBank\CvBank;

class ExperienciaLaboral extends Model
{
    use HasFactory;

    protected $table = 'experiencia_laboral';

    protected $fillable = [
        'tipo_institucion',
        'puesto',
        'institucion',
        'area',
        'remuneracion_mensual',
        'fecha_ingreso',
        'fecha_termino',
        'tiempo_experiencia_especifica',
        'tiempo_experiencia_general',
        'dias_cuenta_regresiva',
        'funciones',
        'motivo_termino',
        'observaciones',
        'imagen',
        'vinculo_laboral_id',
        'modalidad_puesto_id',
        'domain_id',
        'id_postulante'
    ];

    // Relación con la tabla 'vinculo_laboral'
    public function vinculoLaboral()
    {
        return $this->belongsTo(VinculoLaboral::class, 'vinculo_laboral_id');
    }

    // Relación con la tabla 'modalidad_puesto'
    public function modalidadPuesto()
    {
        return $this->belongsTo(ModalidadPuesto::class, 'modalidad_puesto_id');
    }

    // Relación con la tabla 'cv_banks' (postulantes)
    public function postulante()
    {
        return $this->belongsTo(CvBank::class, 'id_postulante');
    }
}
