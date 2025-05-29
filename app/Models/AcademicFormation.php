<?php

namespace App\Models;

use App\Models\CvBank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicFormation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academic_formations';

    protected $fillable = [
        'uuid',
        'cv_bank_id',
        'user_id',
        'education',
        'profession',
        'con',
        'image',
        'status',
        'advance',
        'institute',
        'date_start',
        'date_end',
        'observation',
        'validated',
        'domain_id' 
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? url("storage/$this->image") : null;
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function professionModel()
    {
        return $this->belongsTo(Profesion::class, 'profession');
    }

    public function educationDegree()
    {
        return $this->belongsTo(GradoInstruccion::class, 'education');
    }

    public function advanceStatus()
    {
        return $this->belongsTo(EstadoAvance::class, 'advance');
    }

    public function cvBank()
    {
        return $this->belongsTo(CvBank::class, 'cv_bank_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }
}