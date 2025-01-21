<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class CommandController extends Controller
{
    public function dropApplicant(Request $req){
        return 200;
    }

    public function setCommandUpdate(Request $req){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());

        try{
            switch($req['sett_code']){

                case 'cs_01':
                    $s1 = DB::table('def_command_center')
                    ->where('sett_code','=', $req['sett_code'])
                    ->update([
                        'sett_yearfrom' => $req->input('sett_yearfrom'),
                        'sett_yearto' => $req->input('sett_yearto'),
                        'sett_updatedby' => $req->input('sett_updatedby'),
                        'sett_dateupdated' => $date,
                    ]);
        
                    return $data = [
                        'status' => 200,
                    ];
                break;

                case 'cs_02':
                    $s1 = DB::table('def_command_center')
                    ->where('sett_code','=', $req['sett_code'])
                    ->update([
                        'sett_semester' => $req->input('sett_semester'),
                        'sett_updatedby' => $req->input('sett_updatedby'),
                        'sett_dateupdated' => $date,
                    ]);
        
                    return $data = [
                        'status' => 200,
                    ];
                break;

                case 'cs_03':
                    $s1 = DB::table('def_command_center')
                    ->where('sett_code','=', $req['sett_code'])
                    ->update([
                        'sett_downpayment' => $req->input('sett_downpayment'),
                        'sett_updatedby' => $req->input('sett_updatedby'),
                        'sett_dateupdated' => $date,
                    ]);
        
                    return $data = [
                        'status' => 200,
                    ];
                break;
                
                
                case 'cs_04':
                    $s1 = DB::table('def_command_center_curriculum')
                    ->insert([
                        'sett_course_progid' => $req->input('sett_course_progid'),
                        'sett_course_gradelvl' => $req->input('sett_course_gradelvl'),
                        'sett_course_cid' => $req->input('sett_course_cid'),
                        'sett_course_currid' => $req->input('sett_course_currid'),
                        'sett_course_code' => $req->input('sett_code'),
                        'sett_course_addedby' => $req->input('sett_course_addedby'),
                        'sett_course_dateadded' => $date,
                    ]);
        
                    return $data = [
                        'status' => 200,
                    ];
                break;
            }
        }catch(Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }

    public function getCommandUpdate(){
        $settings = DB::table('def_command_center')
            ->where('sett_status', '=' , 1)
            ->get();
        return $settings; 
    }

    public function getCommandUpdateCurriculum($prog, $grad, $course){
        $settings = DB::table('def_command_center_curriculum')
            ->where('sett_course_status', '=' , 1)
            ->where('sett_course_progid', '=' , $prog)
            ->where('sett_course_gradelvl', '=' , $grad)
            ->where('sett_course_cid', '=' , $course)
            ->get();
        return $settings; 
    }

}
