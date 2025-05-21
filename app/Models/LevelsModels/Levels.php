<?php

namespace App\Models\LevelsModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Levels extends Model
{
    use HasFactory;
    protected $table = "Levels";
    protected $fillable =
    [
        "LevelsId",
        "Description",
        "Orden",
        "Tipo"
    ];
    public function Institutions(){
        return $this->belongToMany(Institutions::class, 'LevesInstitutions');
    }
}
