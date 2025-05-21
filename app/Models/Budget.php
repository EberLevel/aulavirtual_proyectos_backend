<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'BUDGET';

    protected $fillable = [
        'BudgetId',
        'BudgetCode',
        'PositionId',
        'Years',
        'Months',
        'MaxSalary',
        'CurrentSalary',
        'Diference',
        'Indicators',
        'Observation'
    ];
}
