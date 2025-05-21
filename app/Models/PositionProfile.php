<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionProfile extends Model
{
    use SoftDeletes;

    protected $fillable = ['uuid', 'code', 'name', 'area_id', 'institution_id', 'level', 'dependencies', 'profile_levels', 'formation', 'training', 'specific_experience', 'general_experience', 'average_salary', 'position_modality_id', 'current_state_id', 'appointment_date', 'person_name','position_id'];


    public $casts=[
        'appointment_date'=>'date'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function positionModality()
    {
        return $this->belongsTo(ModalidadPuesto::class, 'position_modality_id');
    }

    public function currentState()
    {
        return $this->belongsTo(EstadoActual::class, 'current_state_id');
    }


    public function scopeByPositionId($query, $position_id)
    {
        if ($position_id) {
            return $query->where('position_id', $position_id);
        }

        return $query;
    }

}
