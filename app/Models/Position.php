<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;
    protected $fillable = ['uuid', 'code', 'name', 'phone', 'email', 'area_id', 'institution_id', 'max_salary', 'current_salary', 'difference'];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function scopeByAreaId($query, $area_id)
    {
        if ($area_id) {
            return $query->where('area_id', $area_id);
        }

        return $query;
    }

    public function scopeByTerm($query, $term)
    {
        if ($term) {
            return $query->where('code', 'LIKE', "%$term%")
                ->orWhere('name', 'LIKE', "%$term%")
                ->orWhere('phone', 'LIKE', "%$term%")
                ->orWhere('email', 'LIKE', "%$term%")
                ->orWhere('max_salary', 'LIKE', "%$term%")
                ->orWhere('current_salary', 'LIKE', "%$term%")
                ->orWhere('difference', 'LIKE', "%$term%");
        }

        return $query;
    }
}
