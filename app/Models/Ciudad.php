<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    use HasFactory;

    protected $table = 'ciudades';

    // Incluir todos los campos que deseas permitir llenar
    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
        'observaciones',
        'avance',
        'domain_id',
    ];

    /**
     * Relación con la tabla `av_candidatos`.
     */
    public function candidatos()
    {
        return $this->hasMany(Candidato::class, 'ciudad_id');
    }
}
