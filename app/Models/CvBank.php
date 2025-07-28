<?php

namespace App\Models;

use App\Models\AcademicFormation;
use App\Models\Capacitacion;
use App\Models\DocIdentidad;
use App\Models\EstadoActual;
use App\Models\EstadoCivil;
use App\Models\GradoInstruccion;
use App\Models\Profesion;
use App\Models\Reference;
use App\Models\WorkExperience;
use App\Models\Domains;
use App\Models\User;
use App\Models\Ano;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvBank extends Model
{
    use HasFactory;

    protected $table = 'cv_banks';

    protected $fillable = [
        'user_id',
        'position_code',
        'code',
        'identification_document_id',
        'identification_number',
        'image', // Stores file path
        'nombre_imagen', // Stores image file name
        'cv_path', // Stores CV file path
        'nombre_cv', // Stores CV file name
        'names',
        'phone',
        'marital_status_id',
        'number_children',
        'date_birth',
        'age',
        'education_degree_id',
        'profession_id',
        'ocupacion_actual_id',
        'email',
        'urls',
        'sex',
        'date_affiliation',
        'estado_actual_id',
        'estado_actual_postulante_id', // Nuevo campo
        'domain_id',
        'ano_id', // Nuevo campo
        'color_id',
        'link_facebook',
        'link_instagram',
        'link_tik_tok',
        'nivel_estudios_id'
    ];

    // Relationships
    public function references()
    {
        return $this->hasMany(Reference::class, 'cv_bank_id');
    }

    public function areaPuestos()
    {
        return $this->hasMany(AreaPuesto::class, 'cv_id', 'id');
    }

    public function academic_formations()
    {
        return $this->hasMany(AcademicFormation::class, 'cv_bank_id');
    }

    public function capacitations()
    {
        return $this->hasMany(Capacitacion::class, 'cv_bank_id');
    }

    public function work_experiences()
    {
        return $this->hasMany(WorkExperience::class, 'cv_bank_id');
    }

    public function marital_status()
    {
        return $this->belongsTo(EstadoCivil::class, 'marital_status_id');
    }

    public function profession()
    {
        return $this->belongsTo(Profesion::class, 'profession_id');
    }

    public function estadoActual()
    {
        return $this->belongsTo(EstadoActual::class, 'estado_actual_id');
    }

    // Nueva relación para estado actual postulante
    public function estadoActualPostulante()
    {
        return $this->belongsTo(EstadoActual::class, 'estado_actual_postulante_id');
    }

    public function education_degree()
    {
        return $this->belongsTo(GradoInstruccion::class, 'education_degree_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function identification_document()
    {
        return $this->belongsTo(DocIdentidad::class, 'identification_document_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

    // Nueva relación para año
    public function ano()
    {
        return $this->belongsTo(Ano::class, 'ano_id');
    }

    // Scopes for filtering
    public function scopeByTerm($query, $term)
    {
        if ($term) {
            return $query->where('names', 'LIKE', "%$term%");
        }
    }

    public function scopeByProfessionId($query, $profession_id)
    {
        if ($profession_id) {
            return $query->where('profession_id', $profession_id);
        }
    }

    public function scopeByEducationDegreeId($query, $education_degree_id)
    {
        if ($education_degree_id) {
            return $query->where('education_degree_id', $education_degree_id);
        }
    }

    public function scopeByCurrentStateId($query, $current_state_id)
    {
        if ($current_state_id) {
            return $query->where('estado_actual_id', $current_state_id);
        }
    }

    // Nuevo scope para filtrar por estado actual postulante
    public function scopeByEstadoActualPostulanteId($query, $estado_actual_postulante_id)
    {
        if ($estado_actual_postulante_id) {
            return $query->where('estado_actual_postulante_id', $estado_actual_postulante_id);
        }
    }

    // Nuevo scope para filtrar por año
    public function scopeByAnoId($query, $ano_id)
    {
        if ($ano_id) {
            return $query->where('ano_id', $ano_id);
        }
    }
}