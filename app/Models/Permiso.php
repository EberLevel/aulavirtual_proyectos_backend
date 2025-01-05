<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{

    protected $table="permiso";

    protected $fillable = [
        'id', 'rol_id','nombre','fecha'
    ];


    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'role_permission', 'idpermiso', 'idrol');
    }
    //n-subPermiso
    public function subPermisos()
    {
        return $this->hasMany(Permiso::class, 'permiso_parent_id')->with('subPermisos');
    }
    public function scopeWithRecursiveSelected($query, $rolId, $domainId)
    {
        $selectFields = function($query) use ($rolId, $domainId) {
            return $query->select('permiso.*')
                ->selectRaw('permiso.id=0 as isExpanded')
                ->selectRaw('CASE WHEN rol_permiso.idpermiso IS NOT NULL THEN true ELSE false END as selected')
                ->leftJoin('rol_permiso', function ($join) use ($rolId, $domainId) {
                    $join->on('permiso.id', '=', 'rol_permiso.idpermiso')
                        ->where('rol_permiso.idrol', $rolId);
    
                    // Agregar la condición para domain_id si no es null
                    if (!is_null($domainId)) {
                        $join->where('rol_permiso.domain_id', $domainId);
                    }
                });
        };
    
        return $query->whereNull('permiso_parent_id') // Solo permisos padre
            ->select('permiso.*')
            ->selectRaw('permiso.id=0 as isExpanded')
            ->selectRaw(
                is_null($domainId)
                    ? 'true as selected' // Si domain_id es null, selected siempre será true
                    : 'CASE WHEN rol_permiso.idpermiso IS NOT NULL THEN true ELSE false END as selected'
            )
            ->leftJoin('rol_permiso', function ($join) use ($rolId, $domainId) {
                $join->on('permiso.id', '=', 'rol_permiso.idpermiso')
                    ->where('rol_permiso.idrol', $rolId);
    
                // Agregar la condición para domain_id si no es null
                if (!is_null($domainId)) {
                    $join->where('rol_permiso.domain_id', $domainId);
                }
            })
            ->with(['subPermisos' => function($query) use ($selectFields, $rolId, $domainId) {
                $selectFields($query)->with(['subPermisos' => function($query) use ($selectFields) {
                    $selectFields($query);
                }]);
            }])
            ->orderBy('permiso.id'); // Opcional: ordenar por ID
    }
}
