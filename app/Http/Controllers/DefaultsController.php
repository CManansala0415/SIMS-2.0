<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class DefaultsController extends Controller
{
    
    public function getUserAccess($id)
    {
        $user_access = DB::table('users_access')->orderBy('useracc_id')
        ->where('useracc_accid', '=' , $id)
        ->get();
        return $user_access; 
    }

    public function getGender()
    {
        $gender = DB::table('sett_gender')->orderBy('gen_id')
        ->where('gen_status', '=' , 1)
        ->get();
        return $gender; 
    }

    public function getNationality()
    {
        $nationality = DB::table('sett_nationality')->orderBy('nat_desc')
        ->where('nat_status', '=' , 1)
        ->get();
        return $nationality; 
    }

    public function getCivilStatus()
    {
        $civilstatus = DB::table('sett_civil_status')->orderBy('cv_id')
        ->where('cv_status', '=' , 1)
        ->get();
        return $civilstatus; 
    }

    public function getGradelvl()
    {
        $gradelvl = DB::table('def_gradelvl')->orderBy('grad_dtypeid')
        ->get();
        return $gradelvl;
    }

    public function getProgram()
    {
        $program = DB::table('sett_degree_types')->orderBy('dtype_id')
        ->get();
        return $program;
    }

    public function getSpecialization()
    {
        $specialization = DB::table('sett_specialization')->orderBy('spec_id')
        ->get();
        return $specialization;
    }

    public function getDegree($type = null)
    {
        if(!$type){
            $degree = DB::table('sett_degree')->orderBy('deg_id')
            ->get();
            return $degree;
        }else{
            $degree = DB::table('sett_degree')->orderBy('deg_id')
            ->where('deg_type','=',$type)
            ->get();
            return $degree;
        }
        
    }

    public function getProgramList($id = null)
    {
        if(!$id){
            $programs = DB::table('def_program')
            ->where('prog_status', '=' , 1)
            ->get();
            return $programs;
        }else{
            $programs = DB::table('def_program')
            ->leftJoin('def_program_years', 'def_program.prog_id', '=', 'def_program_years.dprog_progid') 
            ->select(  
                'def_program.*',
                'def_program_years.*',
            )
            ->where('def_program.prog_id','=',$id)
            ->where('def_program.prog_status', '=' , 1)
            ->get();
            return $programs;
        }
    }

    public function getQuarter()
    {
        $quarter = DB::table('sett_quarter')->orderBy('quar_id')
        ->where('sett_quarter.quar_status','=',1)
        ->get();
        return $quarter;
    }

    public function getSemester()
    {
        $semester = DB::table('sett_semester')->orderBy('sem_id')
        ->get();
        return $semester;
    }

    public function getSection()
    {
        $section = DB::table('def_section')->orderBy('sec_id')
        ->get();
        return $section;
    }
    
    public function getCountry()
    {
        $country = DB::table('sett_ph_country')->orderBy('name')
        ->get();
        return $country; 
    }

    public function getRegion()
    {
        $region = DB::table('sett_ph_region')->orderBy('regDesc')
        ->get();
        return $region; 
    }

    public function getProvince()
    {
        $province = DB::table('sett_ph_province')->orderBy('provDesc')
        ->get();
        return $province; 
    }

    public function getCity()
    {
        $city = DB::table('sett_ph_city')->orderBy('citymunDesc')
        ->get();
        return $city; 
    }

    public function getBarangay()
    {
        $barangay = DB::table('sett_ph_barangay')->orderBy('brgyDesc')
        ->get();
        return $barangay; 
    }

    public function getCurriculumSett()
    {
        $curriculum = DB::table('def_curriculum')->orderBy('curr_id')
        ->where('def_curriculum.curr_status','=',1)
        ->get();
        return $curriculum; 
    }

    public function getTaggedSubject()
    {
        
        $subject = DB::table('def_curriculum_tags')
        ->leftJoin('def_subject', 'def_curriculum_tags.currtag_subjid', '=', 'def_subject.subj_id') 
        ->select(  
            'def_subject.*',
            'def_curriculum_tags.*',
        )->orderBy('def_curriculum_tags.currtag_id')
        ->get();
        return $subject;

    }

    public function getSubject()
    {
        
        $subject = DB::table('def_subject as a')
            ->leftJoin('def_subject as b', 'a.subj_preq', '=', 'b.subj_id') 
            ->select(  
                'a.*',
                'b.subj_code as subj_preq_code',
            )
            ->where('a.subj_status', '=' , 1)
            ->orderBy('a.subj_id','ASC')
            ->get();
        return $subject; 

    }

    public function getBuilding()
    {
        $building = DB::table('sett_building')->orderBy('buil_id')
        ->where('buil_status', '=' , 1)
        ->get();
        return $building; 
    }

    public function getClassroom()
    {
        $classroom = DB::table('def_classroom')->orderBy('classr_id')
        ->where('classr_status', '=' , 1)
        ->get();
        return $classroom; 
    }

    public function addProgram(Request $request){

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d h:i:s', time());

        if($request->input('deactivate') == true){
            try{

                $s1 = DB::table('def_program')
                ->where('prog_id','=', $request['prog_id'])
                ->update([
                    'prog_updatedby' => $request->input('prog_updatedby'),
                    'prog_dateupdated' => $date,
                    'prog_status' => 0,
                ]);

                return $data = [
                    'status' => 200,
                ];
            }catch(Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }

        if($request->input('prog_id') == ''){
            try{

                $s1 = DB::table('def_program')->insert([
                    'prog_name' => $request->input('prog_name'),
                    'prog_code' => $request->input('prog_code'),
                    'prog_progtype' => $request->input('prog_progtype'),
                    'prog_degree' => $request->input('prog_degree'),
                    'prog_year' => $request->input('prog_year'),
                    'prog_semtype' => $request->input('prog_semtype'),
                    'prog_semcount' => 2,
                    'prog_addedby' => $request->input('prog_addedby'),
                    'prog_updatedby' => null,
                    'prog_dateadded' => $date,
                    'prog_dateupdated' => null,
                ]);

                return $data = [
                    'status' => 200,
                ];
            }catch(Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }else{
            try{

                $s1 = DB::table('def_program')
                ->where('prog_id','=', $request['prog_id'])
                ->update([
                    'prog_name' => $request->input('prog_name'),
                    'prog_code' => $request->input('prog_code'),
                    'prog_progtype' => $request->input('prog_progtype'),
                    'prog_degree' => $request->input('prog_degree'),
                    'prog_year' => $request->input('prog_year'),
                    'prog_semtype' => $request->input('prog_semtype'),
                    'prog_updatedby' => $request->input('prog_addedby'),
                    'prog_dateupdated' => $date,
                ]);

                return $data = [
                    'status' => 200,
                ];
            }catch(Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
           
        }
        
    }

    public function addSection(Request $request){

        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());
    
            if($request->input('deactivate') == true){
                try{
    
                    $s1 = DB::table('def_section')
                    ->where('sec_id','=', $request['sec_id'])
                    ->update([
                        'sec_updatedby' => $request->input('sec_updatedby'),
                        'sec_dateupdated' => $date,
                        'sec_status' => 0,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
            }
    
            if($request->input('sec_id') == ''){
                try{
    
                    $s1 = DB::table('def_section')->insert([
                        'sec_name' => $request->input('sec_name'),
                        'sec_code' => $request->input('sec_code'),
                        'sec_addedby' => $request->input('sec_addedby'),
                        'sec_updatedby' => null,
                        'sec_dateadded' => $date,
                        'sec_dateupdated' => null,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
            }else{
                try{
    
                    $s1 = DB::table('def_section')
                    ->where('sec_id','=', $request['sec_id'])
                    ->update([
                        'sec_name' => $request->input('sec_name'),
                        'sec_code' => $request->input('sec_code'),
                        'sec_updatedby' => $request->input('sec_addedby'),
                        'sec_dateupdated' => $date,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
               
            }
            
    }

    public function addSubject(Request $request){

        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());
    
            if($request->input('deactivate') == true){
                try{
    
                    $s1 = DB::table('def_subject')
                    ->where('subj_id','=', $request['subj_id'])
                    ->update([
                        'subj_updatedby' => $request->input('subj_updatedby'),
                        'subj_dateupdated' => $date,
                        'subj_status' => 0,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
            }
    
            if($request->input('subj_id') == ''){
                try{
    
                    $s1 = DB::table('def_subject')->insert([
                        'subj_name' => $request->input('subj_name'),
                        'subj_code' => $request->input('subj_code'),
                        'subj_lec' => $request->input('subj_lec'),
                        'subj_lab' => $request->input('subj_lab'),
                        'subj_hrs_week' => $request->input('subj_hrs_week'),
                        'subj_preq' => $request->input('subj_preq'),
                        'subj_dtypeid' => $request->input('subj_dtypeid'),
                        'subj_specid' => $request->input('subj_specid'),
                        'subj_addedby' => $request->input('subj_addedby'),
                        'subj_updatedby' => null,
                        'subj_dateadded' => $date,
                        'subj_dateupdated' => null,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
            }else{
                try{
    
                    $s1 = DB::table('def_subject')
                    ->where('subj_id','=', $request['subj_id'])
                    ->update([
                        'subj_name' => $request->input('subj_name'),
                        'subj_code' => $request->input('subj_code'),
                        'subj_lec' => $request->input('subj_lec'),
                        'subj_lab' => $request->input('subj_lab'),
                        'subj_hrs_week' => $request->input('subj_hrs_week'),
                        'subj_preq' => $request->input('subj_preq'),
                        'subj_dtypeid' => $request->input('subj_dtypeid'),
                        'subj_specid' => $request->input('subj_specid'),
                        'subj_updatedby' => $request->input('subj_addedby'),
                        'subj_dateupdated' => $date,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
            }
    }


    public function addCurriculum(Request $request){

        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());
    
            if($request->input('deactivate') == true){
                try{
    
                    $s1 = DB::table('def_curriculum')
                    ->where('curr_id','=', $request['curr_id'])
                    ->update([
                        'curr_updatedby' => $request->input('curr_updatedby'),
                        'curr_dateupdated' => $date,
                        'curr_status' => 0,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
            }
    
            if($request->input('curr_id') == ''){
                try{
    
                    $s1 = DB::table('def_curriculum')->insert([
                        'curr_code' => $request->input('curr_code'),
                        'curr_from' => $request->input('curr_from'),
                        'curr_to' => $request->input('curr_to'),
                        'curr_progid' => $request->input('curr_progid'),
                        'curr_progtype' => $request->input('curr_progtype'),
                        'curr_tagid' => $request->input('curr_tagid'),
                        'curr_addedby' => $request->input('curr_addedby'),
                        'curr_updatedby' => null,
                        'curr_dateadded' => $date,
                        'curr_dateupdated' => null,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
            }else{
                try{
    
                    $s1 = DB::table('def_curriculum')
                    ->where('curr_id','=', $request['curr_id'])
                    ->update([
                        'curr_code' => $request->input('curr_code'),
                        'curr_from' => $request->input('curr_from'),
                        'curr_to' => $request->input('curr_to'),
                        'curr_progid' => $request->input('curr_progid'),
                        'curr_progtype' => $request->input('curr_progtype'),
                        'curr_tagid' => $request->input('curr_tagid'),
                        'curr_updatedby' => $request->input('curr_addedby'),
                        'curr_dateupdated' => $date,
                    ]);
    
                    return $data = [
                        'status' => 200,
                    ];
                }catch(Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
               
            }
            
    }

    public function addCurriculumTagging(Request $request){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());

        if($request->input('deactivate') == true){
            try{

                $s1 = DB::table('def_curriculum_tags')
                ->where('currtag_id','=', $request['currtag_id'])
                ->update([
                    'currtag_updatedby' => $request->input('currtag_updatedby'),
                    'currtag_dateupdated' => $date,
                    'currtag_status' => 0,
                ]);

                $s2 = DB::table('def_curriculum')
                ->where('curr_id','=', $request['currtag_id'])
                ->update([
                    'curr_updatedby' => $request->input('curr_updatedby'),
                    'curr_dateupdated' => $date,
                    'curr_status' => 0,
                ]);

                return $data = [
                    'status' => 200,
                ];
            }catch(Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }
        
        if($request->input('currtag_id')){
            try{
                $s1 = DB::table('def_curriculum_tags')
                ->where('currtag_id','=', $request['currtag_id'])
                ->update([
                    'currtag_currid' => $request->input('currtag_currid'),
                    'currtag_subjid' => $request->input('subj_id'),
                    'currtag_lec' => $request->input('subj_lec'),
                    'currtag_lab' => $request->input('subj_lab'),
                    'currtag_gradelvl' => $request->input('currtag_gradelvl'),
                    'currtag_sem' => $request->input('currtag_sem'),
                    'currtag_updatedby' => $request->input('currtag_addedby'),
                    'currtag_dateupdated' => $date,
                ]);

                return $data = [
                    'status' => 200,
                ];
            }catch(Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }else{
            try{
    
                $s1 = DB::table('def_curriculum_tags')->insert([
                    'currtag_currid' => $request->input('currtag_currid'),
                    'currtag_subjid' => $request->input('subj_id'),
                    'currtag_lec' => $request->input('subj_lec'),
                    'currtag_lab' => $request->input('subj_lab'),
                    'currtag_gradelvl' => $request->input('currtag_gradelvl'),
                    'currtag_sem' => $request->input('currtag_sem'),
                    'currtag_addedby' => $request->input('currtag_addedby'),
                    'currtag_updatedby' => null,
                    'currtag_dateadded' => $date,
                    'currtag_dateupdated' => null,
                ]);

                return $data = [
                    'status' => 200,
                ];
            }catch(Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }
    }

    public function getEnlistment(){
        $student = DB::table('def_enrollment as enroll')
            ->leftJoin('def_person as student', 'enroll.enr_personid', '=', 'student.per_id') 
            ->leftJoin('def_section as section', 'enroll.enr_section', '=', 'section.sec_id') 
            ->leftJoin('def_program as program', 'enroll.enr_course', '=', 'program.prog_id') 
            ->leftJoin('def_gradelvl as gradelvl', 'enroll.enr_gradelvl', '=', 'gradelvl.grad_id') 
            ->leftJoin('sett_quarter as quarter', 'enroll.enr_quarter', '=', 'quarter.quar_id') 
            ->leftJoin('sett_degree_types as degree', 'enroll.enr_program', '=', 'degree.dtype_id') 
            ->leftJoin('def_curriculum as curriculum', 'enroll.enr_curriculum', '=', 'curriculum.curr_id') 
            ->select(  
                'enroll.*',
                'section.sec_name as section',
                'program.prog_name as course',
                'program.prog_code as course_code',
                'gradelvl.grad_name as gradelvl',
                'quarter.quar_desc as semester',
                'degree.dtype_desc as degree',
                'curriculum.curr_code as curriculum',
                'student.*',
            )->orderBy('student.per_id','DESC')
            ->where('enroll.enr_status', '=' , 1)
            ->get();


        $count = DB::table('def_enrollment as enroll')
            ->leftJoin('def_person as student', 'enroll.enr_personid', '=', 'student.per_id') 
            ->select(  
                'enroll.*',
                'student.*',
            )->where('enroll.enr_status', '=' , 1)
            ->count();
        
        return $data = [
            'data' => $student,
            'count' => $count,
            'status' => 200,
        ];
    }

}
