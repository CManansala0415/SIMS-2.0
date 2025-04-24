<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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

    public function getCommandUsers($userid){
        if($userid == 204){
            $users = DB::table('users')
            ->get();
            $count = DB::table('users')
            ->count();
            return $data = [
                'data' => $users,
                'count' => $count,
            ];

        }else{
            return 200;
        }
    }

    public function updateCommandUsers(Request $req){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());
        try{
            if($req['mode'] == 1){
                $s1 = DB::table('users')
                ->where('id','=', $req['id'])
                ->update([
                    'name' => $req->input('name'),
                    'email' => $req->input('email'),
                    'updated_at' => $date,
                    'updated_by' => $req->input('updated_by'),
                ]);
        
                return $data = [
                    'status' => 200,
                ];
            }else{
                $s2 = DB::table('users')
                ->where('id','=', $req['id'])
                ->update([
                    'status' => 0,
                    'updated_at' => $date,
                    'updated_by' => $req->input('updated_by'),
                ]);
        
                return $data = [
                    'status' => 200,
                ];
            }
        }catch(Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
       
    }

    public function addCommandUsers(Request $req){
        $req->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
        ]);

        if($user){
            return $data = [
                'status' => 200,
            ];
        }else{
            return $data = [
                'status' => 500,
            ];
        }

        
    }

    public function saveCommandAccess(Request $req){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());
        $data = $req->all();
        try{
            $if = '';
            $moduleindex = 0;
            foreach ($data as $module) {
                $moduleindex++;

                // echo $module['module_access'][0]['access_description'];
                foreach ($module['module_access'] as $access) {
                    if($access['useracc_id'] == 0){
                        $s1 = DB::table('users_access') 
                        ->insert([
                            'useracc_accid' => $module['user_id'],
                            'useracc_modulecode' => $module['module_id'],
                            'useracc_accesscode' => $access['access_id'],
                            'useracc_category' => $module['module_category'],
                            'useracc_category_desc' => $module['module_category_desc'],
                            'useracc_viewing' => isset($access['access_viewing'])? $access['access_viewing']:0,
                            'useracc_modifying' => isset($access['access_modifying'])? $access['access_modifying']:0,
                            'useracc_grant' => isset($module['module_grant'])? $module['module_grant']:0,
                            'useracc_addedby' => $module['sett_addedby'],
                            'useracc_dateadded' => $date,
                        ]);
                        $if = 'insert';

                    }else{
                        $s1 = DB::table('users_access') 
                        ->where('useracc_id', '=' , $access['useracc_id'])
                        ->update([
                            'useracc_accid' => $module['user_id'],
                            'useracc_modulecode' => $module['module_id'],
                            'useracc_accesscode' => $access['access_id'],
                            'useracc_category' => $module['module_category'],
                            'useracc_category_desc' => $module['module_category_desc'],
                            'useracc_viewing' => $access['access_viewing'],
                            'useracc_modifying' => $access['access_modifying'],
                            'useracc_grant' => $module['module_grant'],
                            'useracc_addedby' => $module['sett_addedby'],
                            'useracc_dateadded' => $date,
                        ]);
                        $if = 'update';
                    }
                    
                }

            }
    
            return $data = [
                'status' => 200,
                'mode' =>  $if
            ];

        }catch(Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }

    public function getCommandAccess($id){
        $access = DB::table('users_access')
            ->where('useracc_accid', '=' , $id)
            ->get();
        return $access; 
    }

    public function tagEmployeeAccount(Request $req){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());
        try{
            if($req['mode'] == 1){
                $s1 = DB::table('def_employee')
                ->where('emp_id','=', $req['emp_id'])
                ->update([
                    'emp_accid' => $req->input('acc_id'),
                    'emp_dateupdated' => $date,
                    'emp_updatedby' => $req->input('updated_by'),
                ]);
        
                return $data = [
                    'mode' => $req['mode'],
                    'status' => 200,
                ];
            }else{
                $s2 = DB::table('def_employee')
                ->where('emp_id','=', $req['emp_id'])
                ->update([
                    'emp_accid' => 0,
                    'emp_dateupdated' => $date,
                    'emp_updatedby' => $req->input('updated_by'),
                ]);
        
                return $data = [
                    'mode' => $req['mode'],
                    'status' => 200,
                ];
            }
        }catch(Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }

    public function getEmployeeAccount($id){
       if($id == 204){
        $accounts = DB::table('def_employee')
            ->select(  
                'emp_id',
                'emp_accid',)
            ->where('emp_status', '=' , 1)
            ->get();
       }else{
         $accounts = DB::table('def_employee')
            ->select(  
                'emp_id',
                'emp_accid',)
            ->where('emp_id', '=' , $id)
            ->where('emp_status', '=' , 1)
            ->get();
       }
        return $accounts; 
    }
}
