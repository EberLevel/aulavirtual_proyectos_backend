<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbigeoPeruProvince extends Model
{
    use HasFactory;

    protected $table = 'ubigeo_peru_provinces';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'department_id'];

    // Relación con departamento
    public function department()
    {
        return $this->belongsTo(UbigeoPeruDepartment::class, 'department_id', 'id');
    }

    // Relación con distritos
    public function districts()
    {
        return $this->hasMany(UbigeoPeruDistrict::class, 'province_id', 'id');
    }
}
