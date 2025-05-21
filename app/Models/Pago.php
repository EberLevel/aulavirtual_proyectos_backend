<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
