<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
protected $fillable = ['name', 'parent_id', 'description'];

public function parent()
{
return $this->belongsTo(Maestro::class, 'parent_id');
}

public function children()
{
return $this->hasMany(Maestro::class, 'parent_id');
}
}
