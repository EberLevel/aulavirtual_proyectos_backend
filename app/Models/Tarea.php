<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas';

    // Definir que no use timestamps automáticos de Laravel
    public $timestamps = false;

    protected $fillable = [
        'name',
        'created_at',
        'descripcion'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Mutator para asegurar que created_at se establezca al crear
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->created_at) {
                $model->created_at = Carbon::now();
            }
        });
    }

    // Scopes para filtros
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
