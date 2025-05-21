<?php

namespace App\Models\LevelsModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    use HasFactory;
    protected $table = "Areas";
    protected $filleable =
    [
        "AreaId",
        "AreaIdManual",
        "Name",
        "ShortName",
        "TituleName",
        "Responsible",
        "PhoneResponsible",
        "PhoneArea",
        "EmailArea",
        "InsitutionsId"
    ];
    public function lavels()
    {
        return $this->belongToMany(Levels::class, "LevesAreas");
    }
}
