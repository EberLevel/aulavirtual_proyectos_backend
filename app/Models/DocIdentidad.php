<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocIdentidad extends Model
{
    use HasFactory;
    protected $table = 'doc_identidad';

    protected $fillable = [

        'nombre',
        'domain_id',

    ];

    public function domain() {
        return $this->belongsTo(Domains::class, 'domain_id');
    }

}
