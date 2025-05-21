<?php 
namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; 
trait UserTrait{
    public function checkIsValidEmail($email){
        $user=DB::table('users')->where('email',$email)->first();
        if($user){
            return false;
        }
        return true;
    }
}