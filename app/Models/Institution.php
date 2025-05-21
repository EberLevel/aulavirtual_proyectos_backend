<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = ['uuid', 'code', 'name', 'short_name', 'phone', 'email', 'parent_id','level','address','ubigeo','created_by'];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function parent()
    {
        return $this->belongsTo(Institution::class, 'parent_id');
    }

    public function subInstitutions()
    {
        return $this->hasMany(Institution::class, 'parent_id');
    }


    public function scopeByTerm($query, $term)
    {
        if ($term) {
            return $query->where('name', 'LIKE', "%$term%")
                ->orWhere('short_name', 'LIKE', "%$term%")
                ->orWhere('code', 'LIKE', "%$term%")
                ->orWhere('phone', 'LIKE', "%$term%")
                ->orWhere('email', 'LIKE', "%$term%")
                ->orWhere('address', 'LIKE', "%$term%")
                ->orWhere('ubigeo', 'LIKE', "%$term%");
        }

        return $query;
    }
}
