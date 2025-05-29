<?php

namespace App\Models;

use App\Models\CvBank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_order',
        'exp_siaf',
        'certif_siaf',
        'fecha',
        'proveedor',
        'concepto',
        'mon',
        'valor',
        'state_id',
        'cv_bank_id',
    ];

    public function cv_bank()
    {
        return $this->belongsTo(CvBank::class);
    }
}
