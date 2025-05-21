<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoAlumno extends Model
{
    protected $table = 'pago_alumno';
    protected $fillable = [
        'pago_id',
        'alumno_id',
        'estado_id',
        'voucher_pago',
        'comentario'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
