<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{

    protected $table="rol";

    public $timestamps = false;

    protected $fillable = [
        'id', 'nombre', 'fecha', 'domain_id' 
    ];    


    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'idrol', 'idpermiso');
    }
}
