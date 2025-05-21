<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AulaController extends Controller
{
    public function store(Request $request){
        try{
            $toInsert=[
                'domain_id'=>$request->dominio_id,
                'nombre'=>$request->nombre,
                'descripcion'=>$request->direccion,
                'created_at'=>Carbon::now()
            ];
            //if exist id in request, update
            if($request->id){
                DB::table('aulas')->where('id',$request->id)->update($toInsert);
                return response()->json(['status'=>true]);
            }
            DB::table('aulas')->insert($toInsert);
            
            return response()->json(['status'=>true]);
        }
        catch(\Exception $e){
            return response()->json(['status'=>false,'error'=>$e->getMessage()]);
        }
    }
    public function index($dominio_id){
        try{
            $aulas=DB::table('aulas')->where('domain_id',$dominio_id)->get();
            return response()->json(['status'=>true,'data'=>$aulas]);
        }
        catch(\Exception $e){
            return response()->json(['status'=>false,'error'=>$e->getMessage()]);
        }
    }
    public function destroy ($id){
        try{
            DB::table('aulas')->where('id',$id)->delete();
            return response()->json(['status'=>true]);
        }
        catch(\Exception $e){
            return response()->json(['status'=>false,'error'=>$e->getMessage()]);
        }
    }
    public function saveDisponibilidad(Request $request){
        try{
            $aulaId=$request->aulaId;
            $domainId=$request->domainId;
            /**
             * $days=[
             * 'day'=>'2024-08-01',
             * 'hourStart'=>'08:00:00',
             * 'hourEnd'=>'10:00:00'
             * 'dayOfWeek'=>1
             * ]
             */
            $days=$request->events;
            foreach($days as $day){
                $id=array_key_exists('id',$day)?$day['id']:null;
                $toInsert=[
                    'aula_id'=>$aulaId,
                    'domain_id'=>$domainId,
                    'fecha'=>$day['day'],
                    'fecha_day'=>$day['dayWeek'],
                    'hora_inicio'=>$day['hourStart'],
                    'hora_fin'=>$day['hourEnd'],
                    'created_at'=>Carbon::now()
                ];
                if($id){
                    DB::table('aula_availability')->where('id',$id)->update($toInsert);
                    continue;
                }
                DB::table('aula_availability')->insert($toInsert);
            }
            return response()->json(['status'=>true]);
        }
        catch(\Exception $e){
            return response()->json(['status'=>false,'error'=>$e->getMessage()]);
        }
    }
    public function getDisponibilidad($aula_id){
        try{
            $disponibilidad=DB::table('aula_availability')->where('aula_id',$aula_id)->get();
            return response()->json(['status'=>true,'data'=>$disponibilidad]);
        }
        catch(\Exception $e){
            return response()->json(['status'=>false,'error'=>$e->getMessage()]);
        }
    }
    public function destroyDisponibilidad($id){
        try{
            DB::table('aula_availability')->where('id',$id)->delete();
            return response()->json(['status'=>true]);
        }
        catch(\Exception $e){
            return response()->json(['status'=>false,'error'=>$e->getMessage()]);
        }
    }
}
