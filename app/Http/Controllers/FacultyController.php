<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class FacultyController extends Controller
{
    public function getFacultyClass($id){
        $classes = DB::table('def_employee_load as eml')
        ->leftJoin('def_subject as sbj', 'eml.ld_subjid', '=', 'sbj.subj_id')
        ->select(  
            'eml.*',
            'sbj.*',
        )
        ->where('eml.ld_empid', '=',  $id)
        ->orderBy('eml.ld_id','DESC')
        ->get();

        $count = DB::table('def_employee_load as eml')
        ->leftJoin('def_subject as sbj', 'eml.ld_subjid', '=', 'sbj.subj_id')
        ->select(  
            'eml.*',
            'sbj.*',
        )
        ->where('eml.ld_empid', '=',  $id)
        ->count();

        return $data = [
            'data' => $classes,
            'count' => $count,
        ];
    }

    public function getFacultyAssignment($id){
        $assignment = DB::table('def_launch_faculty as laf')
        ->leftJoin('def_subject as sbj', 'laf.lf_subjid', '=', 'sbj.subj_id')
        ->leftJoin('def_launch as lch', 'laf.lf_lnid', '=', 'lch.ln_id')
        ->leftJoin('def_section as sct', 'lch.ln_section', '=', 'sct.sec_id')
        ->leftJoin('def_program as crs', 'lch.ln_course', '=', 'crs.prog_id')
        ->leftJoin('sett_degree_types as sdt', 'lch.ln_dtype', '=', 'sdt.dtype_id') 
        ->leftJoin('def_gradelvl as dgl', 'lch.ln_gradelvl', '=', 'dgl.grad_id') 
        ->select(  
            'laf.*',
            'sbj.*',
            'lch.*',
            'sct.*',
            'crs.*',
            'sdt.dtype_desc',
            'dgl.grad_name',
            
        )
        ->where('laf.lf_empid', '=',  $id)
        ->orderBy('laf.lf_id','DESC')
        ->get();

        $count = DB::table('def_launch_faculty as laf')
        ->leftJoin('def_subject as sbj', 'laf.lf_subjid', '=', 'sbj.subj_id')
        ->leftJoin('def_launch as lch', 'laf.lf_lnid', '=', 'lch.ln_id')
        ->leftJoin('def_section as sct', 'lch.ln_section', '=', 'sct.sec_id')
        ->leftJoin('def_program as crs', 'lch.ln_course', '=', 'crs.prog_id')
        ->select(  
            'laf.*',
            'sbj.*',
            'lch.*',
            'sct.*',
            'crs.*',

        )
        ->where('laf.lf_empid', '=',  $id)
        ->count();

        return $data = [
            'data' => $assignment,
            'count' => $count,
        ];
    }

    public function getFacultyStudent($section, $gradelvl, $course){
        $students = DB::table('def_enrollment as enroll')
        ->leftJoin('def_person as student', 'enroll.enr_personid', '=', 'student.per_id') 
        ->leftJoin('def_section as section', 'enroll.enr_section', '=', 'section.sec_id') 
        ->leftJoin('def_program as program', 'enroll.enr_course', '=', 'program.prog_id') 
        ->leftJoin('def_gradelvl as gradelvl', 'enroll.enr_gradelvl', '=', 'gradelvl.grad_id') 
        ->leftJoin('sett_quarter as quarter', 'enroll.enr_quarter', '=', 'quarter.quar_id') 
        ->leftJoin('sett_degree_types as degree', 'enroll.enr_program', '=', 'degree.dtype_id') 
        ->leftJoin('def_curriculum as curriculum', 'enroll.enr_curriculum', '=', 'curriculum.curr_id') 
        ->leftJoin('def_student_identification as studentid', 'enroll.enr_personid', '=', 'studentid.ident_personid') 
        ->select(  
            'enroll.*',
            'studentid.ident_identification',
            'gradelvl.grad_id',
            'gradelvl.grad_dtypeid',
            'program.prog_id',
            'section.sec_name as section',
            'program.prog_name as course',
            'program.prog_code as course_code',
            'gradelvl.grad_name as gradelvl',
            'quarter.quar_desc as semester',
            'degree.dtype_desc as degree',
            'curriculum.curr_code as curriculum',
            'student.*',
        )->orderBy('student.per_lastname','ASC')
        ->where('enroll.enr_status', '=' , 1)
        ->where('enroll.enr_section', '=' , $section)
        ->where('enroll.enr_gradelvl', '=' , $gradelvl)
        ->where('enroll.enr_course', '=' , $course)
        ->get();


        return $students;
    }

    public function getFacultyStudentMilestone($section, $gradelvl, $course)
    {
        $student = $this->getFacultyStudent($section, $gradelvl, $course);
        $item = json_decode($student, true);
        $array = [];

        foreach ($item as $key => $value) {
            try{
                $milestone = DB::table('def_milestone as a')
                    ->leftJoin('def_enrollment as b', 'a.mi_enrid', '=', 'b.enr_id') 
                    ->leftJoin('def_subject as c', 'a.mi_subjid', '=', 'c.subj_id') 
                    ->leftJoin('def_subject as d', 'c.subj_preq', '=', 'd.subj_id') 
                    ->leftJoin('sett_degree_types as e', 'b.enr_program', '=', 'e.dtype_id') 
                    ->select(  
                        'b.*',
                        'a.*',
                        'c.*',
                        'd.subj_code as subj_preq_code',
                        'e.dtype_desc',
                    )
                    ->orderBy('b.enr_dateenrolled','ASC')
                    ->where('a.mi_enrid', '=' , $value['enr_id'])
                    ->where('a.mi_status', '=' , 1)
                    ->get();

                    array_push($array ,$milestone);
            }

            catch (Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }

        return $data = [
            'milestone' => $array,
            'students' => $student,
            'status' => 200,
        ];
    }

    public function getGradingSheetHeader($id){
        $gradingsheet = DB::table('def_faculty_grading_header')
        ->where('grh_lnid', '=' , $id)
        ->get();
        
        return $gradingsheet;
    }

    public function getGradingSheetGrade($id, $subjid){
        $gradingsheet = DB::table('def_faculty_grading_sheet')
        ->where('grs_headerid', '=' , $id)
        ->where('grs_subjid', '=' , $subjid)
        ->get();
        
        return $gradingsheet;
    }
    

    public function addGradingSheetHeader(Request $request, $type){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');

        try{
            if( $type == 2){
                $primary = DB::table('def_faculty_grading_header')
                ->where('grh_lnid','=', $request->input('grh_lnid'))
                ->delete();

                $secondary = DB::table('def_faculty_grading_sheet')
                ->where('grs_headerid','=', $request->input('grs_headerid'))
                ->delete();
                
                return $primary = [
                    'data' => $request,
                    'status' => 200,
                ];
            }else{
                $primary = DB::table('def_faculty_grading_header')->insert([
                    'grh_lnid' => $request->input('grh_lnid'),
                    'grh_addedby' => $request->input('grh_addedby'),
                    'grh_dateadded' => $date
                ]);
    
                return $primary = [
                    'data' => $request,
                    'status' => 200,
                ];
            }

        } catch (Exception $ex){
            return $data = [
                'status' => 500,
            ];
        }

    }
    public function addGradingSheetGrade(Request $request){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');

        try{
            $data = $request->all();
            foreach ($data as $key => $value) {
                try{
                    if($value['grs_mode']==0){
                        $primary = DB::table('def_faculty_grading_sheet')->insert([
                            'grs_headerid' => $value['grs_headerid'],
                            'grs_subjid' => $value['grs_subjid'],
                            'grs_enrid' => $value['enr_id'],
                            'grs_personid' => $value['per_id'],
                            'grs_prelims' => $value['grade_prelims'],
                            'grs_midterms' => $value['grade_midterms'],
                            'grs_prefinals' => $value['grade_prefinals'],
                            'grs_finals' => $value['grade_finals'],
                            'grs_addedby' => $value['grs_user'],
                            'grs_dateadded' => $date
                        ]);
                    }else{
                        $primary = DB::table('def_faculty_grading_sheet')
                        ->where('grs_id','=', $value['grs_id'])
                        ->update([
                            'grs_headerid' => $value['grs_headerid'],
                            'grs_enrid' => $value['enr_id'],
                            'grs_personid' => $value['per_id'],
                            'grs_prelims' => $value['grade_prelims'],
                            'grs_midterms' => $value['grade_midterms'],
                            'grs_prefinals' => $value['grade_prefinals'],
                            'grs_finals' => $value['grade_finals'],
                            'grs_updatedby' => $value['grs_user'],
                            'grs_dateupdated' => $date
                        ]);
                    }
                    
                }
                catch (Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }
            }

            
            return $primary = [
                'data' => $request,
                'status' => 200,
            ];

        } catch (Exception $ex){
            return $data = [
                'status' => 500,
            ];
        }
    }
}
