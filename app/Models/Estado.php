<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estado extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla
     */
    protected $table = 'estados';

    /**
     * Los atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'nombre',
        'color', 
        'domain_id'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'domain_id' => 'integer'
    ];

    /**
     * Scope para filtrar por dominio
     */
    public function scopePorDominio($query, $domain_id)
    {
        return $query->where('domain_id', $domain_id);
    }
}