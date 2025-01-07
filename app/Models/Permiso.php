<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    public function scopeWithRecursiveSelectedForAdmin($query,$rolId,$domainId){
        //get all left joined with rol_permiso for domain_id
        $selectFields = function($query) use ($rolId, $domainId) {
            return $query->select('permiso.*')
                ->selectRaw('permiso.id=0 as isExpanded')
                ->selectRaw('CASE WHEN rol_permiso.idpermiso IS NOT NULL THEN true ELSE false END as selected')
                ->leftJoin('rol_permiso', function ($join) use ($rolId, $domainId) {
                    $join->on('permiso.id', '=', 'rol_permiso.idpermiso')
                        ->where('rol_permiso.idrol', $rolId)
                        ->where('rol_permiso.domain_id', $domainId);
                    
                });
        };
        return $query->whereNull('permiso_parent_id') // Solo permisos padre
            ->select('permiso.*')
            ->selectRaw('permiso.id=0 as isExpanded')
            ->selectRaw(
                // Si domain_id es null, selected siempre será true
                    'CASE WHEN rol_permiso.idpermiso IS NOT NULL THEN true ELSE false END as selected'
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
    public function scopeWithRecursiveSelected($query, $rolId, $domainId)
    {
        $selectFields = function($query) use ($rolId, $domainId) {
            $query = $query->select('permiso.*')
                ->selectRaw('permiso.id=0 as isExpanded')
                ->selectRaw(
                    is_null($domainId)
                        ? 'true as selected' 
                        : 'CASE WHEN rol_permiso.idpermiso IS NOT NULL THEN true ELSE false END as selected'
                );
        
            if (is_null($domainId)) {
                $query->leftJoin('rol_permiso', function ($join) use ($rolId) {
                    $join->on('permiso.id', '=', 'rol_permiso.idpermiso')
                         ->where('rol_permiso.idrol', $rolId);
                });
            } else {
                $query->join('rol_permiso', function ($join) use ($rolId, $domainId) {
                    $join->on('permiso.id', '=', 'rol_permiso.idpermiso')
                         ->where('rol_permiso.idrol', $rolId)
                         ->where('rol_permiso.domain_id', $domainId);
                });
            }
        
            return $query;
        };
        
        // Primero obtenemos todos los permisos
        $query = $query->whereNull('permiso_parent_id')
            ->select('permiso.*')
            ->selectRaw('permiso.id=0 as isExpanded')
            ->selectRaw(
                is_null($domainId)
                    ? 'true as selected'
                    : 'CASE WHEN rol_permiso.idpermiso IS NOT NULL THEN true ELSE false END as selected'
            )
            ->leftJoin('rol_permiso', function ($join) use ($rolId, $domainId) {
                $join->on('permiso.id', '=', 'rol_permiso.idpermiso')
                     ->where('rol_permiso.idrol', $rolId);
                
                if (!is_null($domainId)) {
                    $join->where('rol_permiso.domain_id', $domainId);
                }
            })
            ->with(['subPermisos' => function($query) use ($selectFields, $rolId, $domainId) {
                $selectFields($query)->with(['subPermisos' => function($query) use ($selectFields, $rolId, $domainId) {
                    $selectFields($query);
                }]);
            }])
            ->orderBy('permiso.id');
        
        // Luego filtramos los que no tienen subpermisos después de obtener los resultados
        return $query->get()->filter(function($permiso) {
            return $permiso->subPermisos->isNotEmpty();
        })->values();
    }
}
