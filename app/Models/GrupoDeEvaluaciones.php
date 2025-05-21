<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoDeEvaluaciones extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grupo_de_evaluaciones';

    protected $fillable = [
        'curso_id',
        'nombre_del_grupo',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at',
        'domain_id'
    ];

    /**
     * Relación con el modelo Curso.
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
    /**
     * Relación con el modelo User para el campo deleted_by.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}