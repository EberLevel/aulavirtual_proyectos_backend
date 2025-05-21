<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Behavior extends Model
{
    use HasFactory;
    protected $table = 'behaviors';

    protected $fillable = [
        'type_document_id',
        'document_number',
        'activity_name',
        'description',
        'date',
        'resources',
        'observation',
        'escala_id',
        'average_behaviors'
    ];

    public function type_documents()
    {
        return $this->belongsTo(Behavior::class, 'type_document_id');
    }

    public function escalas()
    {
        return $this->belongsTo(Behavior::class, 'escala_id');
    }
}
