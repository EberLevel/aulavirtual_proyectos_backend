<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    protected $table = 'evaluations';

    protected $fillable = [
        'cv_bank_id',
        'user_id',
        'position_level_id',
        'scale_id',
        'module',
        'especific_experience',
        'general_experience',
        'time_validated'
    ];

    public function position_level(){
        return $this->belongsTo(NivelCargo::class, 'position_level_id');
    }

    public function scale(){
        return $this->belongsTo(Escala::class, 'scale_id');
    }
}
