<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reunion extends Model
{
    use HasFactory;
    
    protected $table = 'reuniones';

    protected $fillable = [
        'titulo',
        'estado',
        'objetivo',
        'resultado',
        'domain_id'
    ];

    public function domain()
    {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

    public function fotos()
    {
        return $this->hasMany(ReunionFoto::class, 'reunion_id');
    }
}
