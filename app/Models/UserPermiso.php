<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermiso extends Model
{
    protected $table = 'user_permiso'; 
    protected $primaryKey = null; 
    public $incrementing = false;
    public $timestamps = false;

    // Columnas que se pueden rellenar
    protected $fillable = [
        'user_id',
        'permiso_id',
        'domain_id',
        'proyecto_id',
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relación con el modelo Permiso
    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'permiso_id', 'id');
    }

    // Relación con el modelo Domain
    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

    // Relación con el modelo Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id', 'id');
    }
}