<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidato extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'av_candidatos'; // Nombre de la tabla en la base de datos

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'identification_document_id',
        'identification_number',
        'apaterno',
        'amaterno',
        'nombre',
        'phone',
        'marital_status_id',
        'puesto',
        'date_birth',
        'age',
        'education_degree_id',
        'profesion',
        'ocupacion_actual',
        'email',
        'estado_actual',
        'sex',
        'date_affiliation',
        'domain_id',
        'ciudad_id',
        'user_id',
        'image',
        'distrito_id'
    ];    
    /**
     * Relación con la tabla `users`.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la tabla `marital_status`.
     */
    public function marital_status()
    {
        return $this->belongsTo(EstadoCivil::class, 'marital_status_id');
    }

    /**
     * Relación con la tabla `education_degree`.
     */
    public function education_degree()
    {
        return $this->belongsTo(GradoInstruccion::class, 'education_degree_id');
    }

    /**
     * Relación con la tabla `profession`.
     */
    public function profession()
    {
        return $this->belongsTo(Profesion::class, 'profession_id');
    }

    /**
     * Relación con la tabla `ocupacion_actual`.
     */
    public function ocupacionActual()
    {
        return $this->belongsTo(OcupacionActual::class, 'ocupacion_actual_id');
    }

    /**
     * Relación con la tabla `estado_actual`.
     */
    public function estadoActual()
    {
        return $this->belongsTo(EstadoActual::class, 'estado_actual_id');
    }

    /**
     * Relación con la tabla `domains`.
     */
    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

    /**
     * Relación con la tabla `identification_documents`.
     */
    public function identification_document()
    {
        return $this->belongsTo(DocIdentidad::class, 'identification_document_id');
    }

    /**
     * Definir el atributo `urls` como un arreglo JSON.
     */
    protected $casts = [
        'urls' => 'array',
        'date_birth' => 'date',
        'date_affiliation' => 'date',
    ];

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }
    
    /**
     * Scope para filtrar por término.
     */
    public function scopeByTerm($query, $term)
    {
        if ($term) {
            return $query->where('names', 'LIKE', '%' . $term . '%')
                ->orWhere('identification_number', 'LIKE', '%' . $term . '%');
        }
        return $query;
    }

    /**
     * Scope para filtrar por ID de profesión.
     */
    public function scopeByProfessionId($query, $professionId)
    {
        if ($professionId) {
            return $query->where('profession_id', $professionId);
        }
        return $query;
    }

    /**
     * Scope para filtrar por ID de grado de instrucción.
     */
    public function scopeByEducationDegreeId($query, $educationDegreeId)
    {
        if ($educationDegreeId) {
            return $query->where('education_degree_id', $educationDegreeId);
        }
        return $query;
    }

    /**
     * Scope para filtrar por estado actual.
     */
    public function scopeByCurrentStateId($query, $currentStateId)
    {
        if ($currentStateId) {
            return $query->where('estado_actual_id', $currentStateId);
        }
        return $query;
    }

    public function distrito() {
        return $this->belongsTo(UbigeoPeruDistrict::class, 'distrito_id');
    }
}
