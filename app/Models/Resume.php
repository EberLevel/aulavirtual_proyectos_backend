<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'position',
        'time_to_end',
        'qualification',
        'total_time',
        'end_date',
        'p_fce',
        'p_ref',
        'p_punt',
        'salary',
        'work_mode',
        'created_by'
    ];

    /*public function users()
    {
        return $this->belongsTo(users::class, 'user_id');
    }*/

}
