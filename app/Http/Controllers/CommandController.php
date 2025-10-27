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

                case 'cs_07':
                    $codes = ['cs_08', 'cs_09', 'cs_10', 'cs_11'];
                    $userid = $req->input('userid');
                    $mode   = $req->input('mode');

                    // Define the base update fields depending on mode
                    $updateData = [
                        'sett_program'   => $mode == 1 ? 0 : null,
                        'sett_gradelvl'  => $mode == 1 ? 0 : null,
                        'sett_course'    => $mode == 1 ? 0 : null,
                        'sett_updatedby' => $userid,
                        'sett_status'    => $mode == 1 ? 1 : 0,
                        'sett_dateupdated' => $date,
                    ];

                    // Loop through each code and update
                    foreach ($codes as $code) {
                        DB::table('def_command_center')
                            ->where('sett_code', $code)
                            ->update($updateData);
                    }

                    return [
                        'status' => 200,
                    ];
                break;


                case 'cs_08':
                case 'cs_09':
                case 'cs_10':
                case 'cs_11':

                    $s1 = DB::table('def_command_center')
                    ->where('sett_code','=', $req['sett_code'])
                    ->update([
                        'sett_program' => $req->input('sett_course_progid'),
                        'sett_gradelvl' => $req->input('sett_course_gradelvl'),
                        'sett_course' => $req->input('sett_course_cid'),
                        'sett_updatedby' => $req->input('sett_course_addedby'),
                        'sett_status' => $req->input('sett_status'),
                        'sett_dateupdated' => $date,

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
            ->leftJoin('sett_quarter', 'def_command_center.sett_semester', '=', 'sett_quarter.quar_id') 
            ->select(
                'def_command_center.*',
                'sett_quarter.quar_code'
            )
            // ->where('def_command_center.sett_status', '=' , 1)
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
        $request = $req->all();
        try{
            $if = '';
            $moduleindex = 0;
            foreach ($request as $module) {
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
                            'useracc_viewing' => $access['access_viewing'],
                            'useracc_modifying' => $access['access_modifying'],
                            'useracc_grant' => $module['module_grant'],
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
                'mode' =>  $if,
                'data' => $request
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

    public function setAcademicStatus(Request $req){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());

        try{
            if($req['mode'] == 1){
                $s1 = DB::table('def_command_center')
                ->where('sett_code','=', $req->input('code'))
                ->update([
                    'sett_status' => 1,
                    'sett_dateupdated' => $date,
                    'sett_updatedby' => $req->input('userid'),
                ]);
        
                return $data = [
                    'mode' => $req['mode'],
                    'status' => 200,
                ];
            }
            else if($req['mode'] == 2){
                $s2 = DB::table('def_command_center')
                ->where('sett_code','=', $req->input('code'))
                ->update([
                    'sett_status' => 0,
                    'sett_dateupdated' => $date,
                    'sett_updatedby' => $req->input('userid'),
                ]);
        
                return $data = [
                    'mode' => $req['mode'],
                    'status' => 200,
                ];
            }
            else if($req['mode'] == 3){
                
                return $this->generateArchive($date,  $req->input('userid'));

            }else{
                return $data = [
                    'mode' => $req['mode'],
                    'status' => 204,
                ];
            }
        }catch(Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }

    public function getAcademicStatus($mode, $code){
        $s2 = DB::table('def_command_center')
            ->where('sett_code','=', $code)
            ->get();

        return $s2;
    }

    public function generateArchive($date, $userid){
        $controller = new RegistrarController();
        date_default_timezone_set('Asia/Manila');
        $defdate = date('Y-m-d h:i:s', time());
        $defdmy = date('Y-m-d', time());
        $randomizer = md5(rand(1,1000000).uniqid(mt_rand(), true));

        $getschoolyear = DB::table('def_command_center')
            ->where('sett_code','=', 'cs_01')
            ->first();
        
        //save person data
        $persons = DB::table('def_enrollment')
                    ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 

                    ->leftJoin('sett_ph_country as birthcountry', 'def_person.per_birth_country', '=', 'birthcountry.countryCode') 
                    ->leftJoin('sett_ph_province as birthprovince', 'def_person.per_birth_province', '=', 'birthprovince.provCode') 

                    ->leftJoin('sett_ph_country as currcountry', 'def_person.per_curr_country', '=', 'currcountry.countryCode') 
                    ->leftJoin('sett_ph_province as currprovince', 'def_person.per_curr_province', '=', 'currprovince.provCode') 
                    ->leftJoin('sett_ph_city as currcity', 'def_person.per_curr_city', '=', 'currcity.citymunCode') 
                    ->leftJoin('sett_ph_barangay as currbarangay', 'def_person.per_curr_barangay', '=', 'currbarangay.brgyCode') 

                    ->leftJoin('sett_ph_country as permcountry', 'def_person.per_perm_country', '=', 'permcountry.countryCode') 
                    ->leftJoin('sett_ph_province as permprovince', 'def_person.per_perm_province', '=', 'permprovince.provCode') 
                    ->leftJoin('sett_ph_city as permcity', 'def_person.per_perm_city', '=', 'permcity.citymunCode') 
                    ->leftJoin('sett_ph_barangay as permbarangay', 'def_person.per_perm_barangay', '=', 'permbarangay.brgyCode') 

                    ->leftJoin('sett_quarter', 'def_enrollment.enr_quarter', '=', 'sett_quarter.quar_id')
                    ->leftJoin('def_gradelvl', 'def_enrollment.enr_gradelvl', '=', 'def_gradelvl.grad_id') 
                    ->leftJoin('sett_degree_types', 'def_enrollment.enr_program', '=', 'sett_degree_types.dtype_id') 
                    ->leftJoin('def_program', 'def_enrollment.enr_course', '=', 'def_program.prog_id') 
                    ->leftJoin('def_student_identification', 'def_person.per_id', '=', 'def_student_identification.ident_personid') 
                    ->leftJoin('def_curriculum', 'def_enrollment.enr_curriculum', '=', 'def_curriculum.curr_id') 
                    ->leftJoin('def_section', 'def_enrollment.enr_section', '=', 'def_section.sec_id') 

                    ->select(  
                        'def_enrollment.enr_id',
                        'def_person.*',
                        
                        'birthcountry.name as birthcountryname',
                        'birthprovince.provDesc as birthprovincename',

                        'currcountry.name as currcountryname',
                        'currprovince.provDesc as currprovincename',
                        'currcity.citymunDesc as currcityname',
                        'currbarangay.brgyDesc as currbarangayname',

                        'permcountry.name as permcountryname',
                        'permprovince.provDesc as permprovincename',
                        'permcity.citymunDesc as permcityname',
                        'permbarangay.brgyDesc as permbarangayname',

                        'sett_quarter.quar_desc as semester',
                        'def_gradelvl.grad_name as gradelvl',
                        'sett_degree_types.dtype_desc as program',
                        'def_program.prog_name as coursename',
                        'def_program.prog_code as coursecode',
                        'def_student_identification.ident_identification as studentid',
                        'def_student_identification.ident_lrn as studentlrn',
                        'def_section.sec_name as section',
                        'def_curriculum.curr_code as curriculum',

                    )
                    ->where('def_person.per_status', '=' ,  1)
                    ->get();   

        if($persons){
            
            foreach ($persons as $key => $details) {
                $load = 'arc-' .$defdmy.  '-' . $details->per_personid. '-' .$randomizer;
                $primary = DB::table('server_archive_persons')->insert([
                    'arc_studentid' => $details->studentid,
                    'arc_studentlrn' => $details->studentlrn,
                    'arc_personid' => $details->per_id,
                    'arc_firstname' => $details->per_firstname,
                    'arc_middlename' => $details->per_middlename,
                    'arc_lastname' => $details->per_lastname,
                    'arc_suffixname' => $details->per_suffixname,
                    'arc_birthday' => $details->per_birthday,
                    'arc_birth_country' => $details->birthcountryname,
                    'arc_birth_province' => $details->birthprovincename,
                    'arc_birth_zipcode' => $details->per_birth_zipcode,
                    'arc_gender' => $details->per_gender,
                    'arc_civilstatus' => $details->per_civilstatus,
                    'arc_nationality' => $details->per_nationality,
                    'arc_contact' => $details->per_contact,
                    'arc_email' => $details->per_email,
                    'arc_curr_home' => $details->per_curr_home,
                    'arc_curr_country' => $details->currcountryname,
                    // 'arc_curr_region' => $details->currregionname,
                    'arc_curr_province' => $details->currprovincename,
                    'arc_curr_city' => $details->currcityname,
                    'arc_curr_barangay' => $details->currbarangayname,
                    'arc_curr_zipcode' => $details->per_curr_zipcode,
                    'arc_perm_home' => $details->per_perm_home,
                    'arc_perm_country' => $details->permcountryname,
                    // 'arc_perm_region' => $details->permregionname,
                    'arc_perm_province' => $details->permprovincename,
                    'arc_perm_city' => $details->permcityname,
                    'arc_perm_barangay' => $details->permbarangayname,
                    'arc_perm_zipcode' => $details->per_perm_zipcode,
                    'arc_dateapplied' => $details->per_dateapplied,
                    'arc_person_dategenerated' => $defdate,
                    'arc_person_generatedby' => $userid,
                    'arc_subjects' => $load,
                    'arc_gradelvl' => $details->gradelvl,
                    'arc_semester' => $details->semester,
                    'arc_schoolyear' => 'SY '.$getschoolyear->sett_yearfrom.'-'.$getschoolyear->sett_yearto,
                    'arc_fullname' => $details->per_lastname.', '.$details->per_firstname.' '.$details->per_middlename.' '.$details->per_suffixname,

                ]);    

                $grades = $controller->getMilestone($details->enr_id);
                foreach ($grades as $key => $mark) {
                    $primary = DB::table('server_archive_subjects')->insert([
                        'arc_archiveid' => $load,
                        'arc_personid' => $details->per_id,
                        'arc_studentid' => $details->studentid,
                        'arc_enrid' => $details->enr_id,
                        'arc_coursecode' => $details->coursecode,
                        'arc_coursename' => $details->coursename,
                        'arc_yearlevel' => $details->gradelvl,
                        'arc_program' => $details->program,
                        'arc_semester' => $details->semester,
                        'arc_section' => $details->section,
                        'arc_curriculum' => $details->curriculum,
                        'arc_subjectcode' => $mark->subj_code,
                        'arc_subjectname' => $mark->subj_name,
                        'arc_lecture' => $mark->subj_lec,
                        'arc_laboratory' => $mark->subj_lab,
                        'arc_units' => $mark->subj_lec+$mark->subj_lab,
                        'arc_dateenrolled' => $mark->enr_dateenrolled,
                        'arc_prelimgrade' => $mark->grs_prelims,
                        'arc_midtermgrade' => $mark->grs_midterms,
                        'arc_prefinalgrade' => $mark->grs_prefinals,
                        'arc_finalgrade' => $mark->grs_finals,
                        'arc_facultyname' => $mark->emp_firstname.' '.$mark->emp_middlename.' '.$mark->emp_lastname.' '.$mark->emp_suffixname,
                        'arc_taken_dategenerated' => $date,
                        'arc_taken_generatedby' => $userid,
                    ]);    
                }
            }
        }

        // truncate these table to start new tables in fresh state after backuping important information
        DB::table('def_enrollment')->truncate();
        DB::table('def_milestone')->truncate();
        DB::table('def_faculty_grading_header')->truncate();
        DB::table('def_faculty_grading_sheet')->truncate();
        DB::table('def_launch')->truncate();
        DB::table('def_launch_faculty')->truncate();
        DB::table('def_employee_load')->truncate();

        return $data = [
            'date' => $date,
            'userid' => $userid,
            'persons' => $persons,
            'status' => 200,
        ];
    }

    public function updateArchiveDetails(Request $req) {
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());

        $s1 = DB::table('server_archive_persons')
                    ->where('arc_personid','=', $req['arc_personid'])
                    ->update([
                    'arc_firstname' => $req['arc_firstname'],
                    'arc_middlename' => $req['arc_middlename'],
                    'arc_lastname' => $req['arc_lastname'],
                    'arc_suffixname' => $req['arc_suffixname'],
                    'arc_birthday' => $req['arc_birthday'],
                    'arc_gender' => $req['arc_gender'],
                    'arc_civilstatus' => $req['arc_civilstatus'],
                    'arc_nationality' => $req['arc_nationality'],
                    'arc_contact' => $req['arc_contact'],
                    'arc_email' => $req['arc_email'],
                    'arc_curr_home' => $req['arc_curr_home'],
                    'arc_curr_country' => $req['arc_curr_country'],
                    'arc_curr_region' => $req['arc_curr_region'],
                    'arc_curr_province' => $req['arc_curr_province'],
                    'arc_curr_city' => $req['arc_curr_city'],
                    'arc_curr_barangay' => $req['arc_curr_barangay'],
                    'arc_curr_zipcode' => $req['arc_curr_zipcode'],
                    'arc_perm_home' => $req['arc_perm_home'],
                    'arc_perm_country' => $req['arc_perm_country'],
                    'arc_perm_region' => $req['arc_perm_region'],
                    'arc_perm_province' => $req['arc_perm_province'],
                    'arc_perm_city' => $req['arc_perm_city'],
                    'arc_perm_barangay' => $req['arc_perm_barangay'],
                    'arc_perm_zipcode' => $req['arc_perm_zipcode'],
                    'arc_dateupdated' => $date,
                    'arc_updatedby' => $req['$arc_updatedby'],
                ]);  
                
        return $data = [
            'status' => 200,
        ];  
    }
}


