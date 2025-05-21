<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbigeoPeruDepartment extends Model
{
    use HasFactory;

    // Como el id no es autoincremental, se debe especificar la clave primaria y desactivar incremento
    protected $table = 'ubigeo_peru_departments';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Campos permitidos para asignación masiva
    protected $fillable = ['id', 'name'];

    // Relación con provincias
    public function provinces()
    {
        return $this->hasMany(UbigeoPeruProvince::class, 'department_id', 'id');
    }

    // Relación con distritos
    public function districts()
    {
        return $this->hasMany(UbigeoPeruDistrict::class, 'department_id', 'id');
    }
}
