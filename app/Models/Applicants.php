<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicants extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'Applicants';

    protected $fillable = [
        'ApplicantsId',
        'StaffId',
        'ManuelCode',
        'DocumentType',
        'DocumentNumber',
        'Photo',
        'Names',
        'Phone',
        'CivilStatus',
        'Children',
        'BirthDate',
        'Age',
        'DegreeInstruction',
        'Profession',
        'ProfessionOther',
        'Email',
        'Url',
        'ProfileProfessional'
    ];
}
