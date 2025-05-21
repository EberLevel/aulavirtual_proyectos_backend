<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReunionFoto extends Model
{
    use HasFactory;
    
    protected $table = 'reunion_fotos';

    protected $fillable = [
        'foto_base64',
        'reunion_id',
    ];

    public function reunion()
    {
        return $this->belongsTo(Reunion::class, 'reunion_id');
    }
}
