<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PagoAlumno;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $fillable = [
        'nombre',
        'descripcion',
        'monto',
        'fecha_pago',
        'fecha_vencimiento',
        'estado_id',
        'domain_id'
    ];

    public function estados()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function pagoAlumnos()
        {
            return $this->hasMany(PagoAlumno::class, 'pago_id');
        }
}
