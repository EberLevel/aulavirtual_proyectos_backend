<?php

namespace App\Models\CvBank;

use Illuminate\Database\Eloquent\Model;
use App\Models\AcademicFormation;
use App\Models\Capacitation;
use App\Models\DocIdentidad;
use App\Models\EstadoActual;
use App\Models\EstadoCivil;
use App\Models\GradoInstruccion;
use App\Models\Maintenance\Profession;
use App\Models\Profesion;
use App\Models\Reference;
use App\Models\WorkExperience;
use App\Models\Domains;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CvBank extends Model
{
    use HasFactory;

    protected $table = 'cv_banks';

    protected $fillable = [
        'id',
        'user_id',
        'position_code',
        'code',
        'identification_document_id',
        'identification_number',
        'image',
        'names',
        'phone',
        'marital_status_id',
        'number_children',
        'date_birth',
        'age',
        'education_degree_id',
        'profession_id',
        'email',
        'urls',
        'sex',
        'date_affiliation',
        'estado_actual_id',
        'training_type_id',
        'ocupacion_actual_id',
        'domain_id',
        'imagen'
    ];

    // Relaciones con otras tablas

    public function references()
    {
        return $this->hasMany(Reference::class, 'cv_bank_id');
    }

    public function academic_formations()
    {
        return $this->hasMany(AcademicFormation::class, 'cv_bank_id');
    }

    public function capacitations()
    {
        return $this->hasMany(Capacitation::class, 'cv_bank_id');
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

    public function education_degree()
    {
        return $this->belongsTo(GradoInstruccion::class, 'education_degree_id');
    }

    public function identification_document()
    {
        return $this->belongsTo(DocIdentidad::class, 'identification_document_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

    // Scopes para filtrar los resultados

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
}
