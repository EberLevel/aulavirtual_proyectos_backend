<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;
    protected $table = 'config';

    protected $fillable = [
        'name',
        'acronym',
        'name_governor',
        'cargo',
        'logo',
        'background',
        'text',
    ];

        // nueva propiedad con la url de la imagen

        protected $appends = ['logo_url'];

        public function getLogoUrlAttribute()
        {
            return $this->logo ? url("storage/$this->logo") : null;
        }
}
