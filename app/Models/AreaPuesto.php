<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaPuesto extends Model
{
    protected $table = 'area_puestos';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function cvBank()
    {
        return $this->belongsTo(CvBank::class, 'cv_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo(InstitucionArea::class, 'area_id', 'id');
    }

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id', 'id');
    }

    public function continuidad()
    {
        return $this->belongsTo(PuestoContinuidad::class, 'continuidad_id', 'id');
    }

    public function permanencia()
    {
        return $this->belongsTo(PuestoPermanencia::class, 'permanencia_id', 'id');
    }
}