<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $table = 'work_experience';

    protected $fillable = [
        'uuid',
        'user_id',
        'cv_bank_id',
        'position_code',
        'institution_type',
        'institution',
        'area',
        'position',
        'functions',
        'employment_link_id',
        'position_modality_id',
        'salary',
        'start_at',
        'end_at',
        'especific_experience',
        'general_experience',
        'countdown_days',
        'image',
        'end_reason',
        'observations',
        'domain_id',
        'validated'
    ];

    // nueva propiedad con la url de la imagen

    protected $appends = ['image_url'];

    public function domain() {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? url("storage/$this->image") : null;
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function position_modality()
    {
        return $this->belongsTo(ModalidadPuesto::class, 'position_modality_id', 'id');
    }

    public function employment_link()
    {
        return $this->belongsTo(VinculoLaboral::class, 'employment_link_id', 'id');
    }

}
