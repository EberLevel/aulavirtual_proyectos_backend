<?php

namespace App\Models;

use App\Models\CvBank;
use App\Models\Maintenance\ManagementDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDocumentaryFile extends Model
{
    use HasFactory;

    protected $table = "personal_documentary_files";
    protected $fillable = [
        'management_document_type_id',
        'activity_name',
        'document_number',
        'description',
        'observations',
        'fecha',
        'resources',
        'cv_bank_id'
    ];

    // nueva propiedad con la url de la imagen

    protected $appends = ['resources_url'];

    public function getResourcesUrlAttribute()
    {
        return $this->resources ? url("storage/$this->resources") : null;
    }

    public function managementDocumentType()
    {
        return $this->belongsTo(TipoDocumentoDeGestion::class, 'management_document_type_id');
    }

    public function cv_bank()
    {
        return $this->belongsTo(CvBank::class);
    }
}
