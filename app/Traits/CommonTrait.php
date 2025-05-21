<?php 
namespace App\Traits;
use illuminate\Support\Facades\DB;
trait CommonTrait{
    public function getRolesDropDownTrait(){
        $roles=DB::table('rol')->select('id','nombre')->whereNot('id',1)->get();
        return $roles;
    }
    
}