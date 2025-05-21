<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationFinal extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_bank_id',
        'user_id',
        'module_status_management',
        'action_id',
        'aceptation_id',
        'position_level_id',
        'scale_id',
        'institution'
    ];
}
