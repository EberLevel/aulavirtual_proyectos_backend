<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    use HasFactory;
    protected $table = 'references';

    protected $fillable = [
        'user_id',
        'cv_bank_id',
        'description',
        'phone',
        'reason',
        'ocupation',
        'domain_id',
        'type'
    ];


    public function domain() {
        return $this->belongsTo(Domains::class, 'domain_id');
    }


    public function scopeByType($query, $type)
    {
        if ($type) {
            return $query->where('type', $type);
        }
    return $query->where('type', $type);
    }
}
