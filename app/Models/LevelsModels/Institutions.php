<?php

namespace App\Models\LevelsModels;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institutions extends Model
{
    use HasFactory;
    protected $table = "Institutions";
    protected $fillable =
    [
        "InstitutionsId",
        "Code",
        "Name",
        "ShortName",
        "Responsible",
        "InstitutionalPhone",
        "DependencyEmail",
        "Ubigeo"
    ];

    public function levels()
    {
        return $this->belongToMany(Levels::class, 'LevesInstitutions');
    }
    public function areas()
    {
        return $this->hasMany(Area::class, 'InstitutionId');
    }
}
