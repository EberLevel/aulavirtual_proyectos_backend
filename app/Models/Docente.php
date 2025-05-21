<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docentes';
    protected $fillable = 
    [
        'codigo',
        'nombres',
        'usuario',
        'clave',
        'celular',
        'profesion',
        'tipo_documento',
        'doc_identidad',
        'fecha_nacimiento',
        'edad',
        'genero',
        'foto',
        'roles',
        'domain_id',
        'email',
    ];
}
