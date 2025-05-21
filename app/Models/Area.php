<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = ['uuid', 'code', 'name', 'short_name', 'phone', 'email', 'institution_id', 'parent_id', 'address', 'ubigeo', 'level'];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }


    public function scopeByTerm($query, $term)
    {
        if ($term) {
            return $query->where('name', 'LIKE', "%$term%")
                ->orWhere('short_name', 'LIKE', "%$term%")
                ->orWhere('code', 'LIKE', "%$term%")
                ->orWhere('phone', 'LIKE', "%$term%")
                ->orWhere('email', 'LIKE', "%$term%");
        }

        return $query;
    }

    public function parent()
    {
        return $this->belongsTo(Area::class, 'parent_id');
    }


    public function subAreas()
    {
        return $this->hasMany(Area::class, 'parent_id');
    }

    public function scopeByInstitutionId($query, $institution_id)
    {
        if ($institution_id) {
            return $query->where('institution_id', $institution_id);
        }

        return $query;
    }

    public function scopeByAreaId($query, $parent_id)
    {
        if ($parent_id) {
            return $query->where('parent_id', $parent_id);
        }

        return $query;
    }
}
