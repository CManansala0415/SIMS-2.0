<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class RegistrarController extends Controller
{
    public function getPerson($id)
    {
        $applicant = DB::table('def_person')
                          ->where('per_id', '=',  $id)
                          ->where('per_status', '=',  1)
                          ->first();
        return $applicant;
    }

    public function getFamily($id)
    {
        $family = DB::table('def_person_family')
                          ->where('fam_personid', '=', $id)
                          ->where('fam_status', '=',  1)
                          ->get();
        return $family;
    }

    public function getAttainment($id)
    {
        $attainment = DB::table('def_person_attainment')
                          ->where('pered_personid', '=', $id)
                          ->where('pered_status', '=',  1)
                          ->get();
        return $attainment;
    }

    public function getAward($id)
    {
        $award = DB::table('def_person_award')
                          ->where('awr_personid', '=', $id)
                          ->where('awr_status', '=',  1)
                          ->get();
        return $award;
    }

    public function getApplicant($limit, $offset, $search)
    {   
        if($limit == 0 && $offset == 0){
            $applicant = DB::table('def_person')->orderBy('per_id','DESC')
                        ->where('per_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('per_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('per_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('per_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('per_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('per_dateapplied', 'like',  '%' . $search .'%');
                        })
                        ->get();
            $count =  DB::table('def_person')->orderBy('per_id','DESC')
                        ->where('per_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('per_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('per_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('per_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('per_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('per_dateapplied', 'like',  '%' . $search .'%');
                        })
                        ->count();       

            return $data = [
                'data' => $applicant,
                'count' => $count,
            ];

        }else{
            if($search==204){
                $applicant = DB::table('def_person')->where('per_status', '=',  1)->orderBy('per_id','DESC')->limit($limit)->offset($offset)->get();
                $count = DB::table('def_person')->count();
            }
            else{
                $applicant = DB::table('def_person')->orderBy('per_id','DESC')
                            ->where('per_status', '=',  1)
                            ->where(function($query) use ($search) {
                                $query->where('per_firstname', 'like',  '%' . $search .'%')
                                ->orWhere('per_middlename', 'like',  '%' . $search .'%')
                                ->orWhere('per_lastname', 'like',  '%' . $search .'%')
                                ->orWhere('per_suffixname', 'like',  '%' . $search .'%')
                                ->orWhere('per_dateapplied', 'like',  '%' . $search .'%');
                            })
                            ->limit($limit)->offset($offset)
                            ->get();
                $count =  DB::table('def_person')->orderBy('per_id','DESC')
                            ->where('per_status', '=',  1)
                            ->where(function($query) use ($search) {
                                $query->where('per_firstname', 'like',  '%' . $search .'%')
                                ->orWhere('per_middlename', 'like',  '%' . $search .'%')
                                ->orWhere('per_lastname', 'like',  '%' . $search .'%')
                                ->orWhere('per_suffixname', 'like',  '%' . $search .'%')
                                ->orWhere('per_dateapplied', 'like',  '%' . $search .'%');
                            })
                            ->limit($limit)->offset($offset)
                            ->count();       
            }
    
            return $data = [
                'data' => $applicant,
                'count' => $count,
            ];
        }
        
        
    }

    public function addApplicant(Request $request, $type)
    {
        switch($type){
            case 1:
                $format1 = substr($request->input('per_firstname'), 0, 2);
                $format2 = substr($request->input('per_middlename'), 0, 2);
                $format3 = substr($request->input('per_lastname'), 0, 2);

                $randomizer = uniqid();

                $famid = 'fam-' .strtoupper($format1) . strtoupper($format2) . strtoupper($format3) . '-' .  $randomizer;
                $awrid = 'awr-' .strtoupper($format1) . strtoupper($format2) . strtoupper($format3) . '-' .  $randomizer;
                $attid = 'att-' .strtoupper($format1) . strtoupper($format2) . strtoupper($format3) . '-' .  $randomizer;
                $perid = 'per-' .strtoupper($format1) . strtoupper($format2) . strtoupper($format3) . '-' .  $randomizer;

                try{
                    //date time saving last to fix naten
                    date_default_timezone_set('Asia/Manila');
                    $date = date('Y-m-d h:i:s', time());
                    $primary = DB::table('def_person')->insert([
                        'per_firstname' => $request->input('per_firstname'),
                        'per_middlename' => $request->input('per_middlename'),
                        'per_lastname' => $request->input('per_lastname'),
                        'per_suffixname' => $request->input('per_suffixname'),
                        'per_birthday' => $request->input('per_birthday'),
                        'per_birth_country' => $request->input('per_birth_country'),
                        'per_birth_province' => $request->input('per_birth_province'),
                        'per_birth_city' => $request->input('per_birth_city'),
                        'per_birth_zipcode' => $request->input('per_birth_zipcode'),
                        'per_gender' => $request->input('per_gender'),
                        'per_civilstatus' => $request->input('per_civilstatus'),
                        'per_nationality' => $request->input('per_nationality'),
                        'per_contact' => $request->input('per_contact'),
                        'per_email' => $request->input('per_email'),
                        'per_curr_home' => $request->input('per_curr_home'),
                        'per_curr_region' => $request->input('per_curr_region'),
                        'per_curr_province' => $request->input('per_curr_province'),
                        'per_curr_city' => $request->input('per_curr_city'),
                        'per_curr_barangay' => $request->input('per_curr_barangay'),
                        'per_curr_zipcode' => $request->input('per_curr_zipcode'),
                        'per_perm_home' => $request->input('per_perm_home'),
                        'per_perm_region' => $request->input('per_perm_region'),
                        'per_perm_province' => $request->input('per_perm_province'),
                        'per_perm_city' => $request->input('per_perm_city'),
                        'per_perm_barangay' => $request->input('per_perm_barangay'),
                        'per_perm_zipcode' => $request->input('per_perm_zipcode'),
                        'per_educid' => $awrid,
                        'per_famid' => $famid,
                        'per_attainmentid' => $attid,
                        'per_personid' => $perid,
                        'per_regtype' => $request->input('per_regtype'),
                        'per_dateapplied' =>$date,
                        'per_user' =>$request->input('per_user'),
                        
                    ]);

                    return $data = [
                        'fam_id' => $famid,
                        'educ_id' => $awrid,
                        'att_id' => $attid,
                        'status' => 204,
                    ];
                }
                catch (Exception $ex) {
                    return $data = [
                        'status' => 500,
                    ];
                }

                
            break;
            case 2:
                // $data = $request->all();
                // foreach ($data as $key => $value) {
                    try{
                        if(empty($request->input('fam_id'))){
                            $primary = DB::table('def_person_family')->insert([
                                'fam_personid' => $request->input('fam_personid'),
                                'fam_firstname' => $request->input('fam_firstname'),
                                'fam_middlename' => $request->input('fam_middlename'),
                                'fam_lastname' => $request->input('fam_lastname'),
                                'fam_suffixname' => $request->input('fam_suffixname'),
                                'fam_relationship' => $request->input('fam_relationship'),
                                'fam_contact' => $request->input('fam_contact'),
                                'fam_email' => $request->input('fam_email'),
                                'fam_user' => $request->input('fam_user'),
                            ]);
                        }
                        return $data = [
                            'data' => $request,
                            'status' => 200,
                        ];
                    }
                    catch (Exception $ex) {
                        return $data = [
                            'status' => 500,
                        ];
                    }
                // }
               
            break;
            case 3:
                // $data = $request->all();
                // foreach ($data as $key => $value) {
                    try{
                        if(empty($request->input('awr_id'))){
                            $primary = DB::table('def_person_award')->insert([
                                'awr_personid' => $request->input('awr_personid'),
                                'awr_desc' => $request->input('awr_desc'),
                                'awr_year' => $request->input('awr_year'),
                                'awr_user' => $request->input('awr_user'),
                            ]);
                        }
                        return $data = [
                            'data' => $request,
                            'status' => 200,
                        ];
                    }
                    catch (Exception $ex) {
                        return $data = [
                            'status' => 500,
                        ];
                    }
                // }
               
            break;
            case 4:
                // $data = $request->all();
                // foreach ($data as $key => $value) {
                    try{
                        if(empty($request->input('pered_id'))){
                            $primary = DB::table('def_person_attainment')->insert([
                                'pered_personid' => $request->input('pered_personid'),
                                'pered_school' => $request->input('pered_school'),
                                'pered_from' => $request->input('pered_from'),
                                'pered_to' => $request->input('pered_to'),
                                'pered_user' => $request->input('pered_user'),
                            ]);
                        }
                        return $data = [
                            'data' => $request,
                            'status' => 200,
                        ];
                    }
                    catch (Exception $ex) {
                        return $data = [
                            'status' => 500,
                        ];
                    }
                // }
                
            break;
        }
    }
    public function updateApplicant(Request $req)
    {
        try{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $s1 = DB::table('def_person')
                ->where('per_id','=', $req['per_id'])
                ->update([
                    "per_id" => $req['per_id'],
                    "per_firstname" => $req['per_firstname'],
                    "per_middlename" => $req['per_middlename'],
                    "per_lastname" => $req['per_lastname'],
                    "per_suffixname" => $req['per_suffixname'],
                    "per_birthday" => $req['per_birthday'],
                    "per_birth_country" => $req['per_birth_country'],
                    "per_birth_province" => $req['per_birth_province'],
                    "per_birth_city" => $req['per_birth_city'],
                    "per_birth_zipcode" => $req['per_birth_zipcode'],
                    "per_gender" => $req['per_gender'],
                    "per_civilstatus" => $req['per_civilstatus'],
                    "per_nationality" => $req['per_nationality'],
                    "per_contact" => $req['per_contact'],
                    "per_email" => $req['per_email'],
                    "per_curr_home" => $req['per_curr_home'],
                    "per_curr_country" => $req['per_curr_country'],
                    "per_curr_region" => $req['per_curr_region'],
                    "per_curr_province" => $req['per_curr_province'],
                    "per_curr_city" => $req['per_curr_city'],
                    "per_curr_barangay" => $req['per_curr_barangay'],
                    "per_curr_zipcode" => $req['per_curr_zipcode'],
                    "per_perm_home" => $req['per_perm_home'],
                    "per_perm_country" => $req['per_perm_country'],
                    "per_perm_region" => $req['per_perm_region'],
                    "per_perm_province" => $req['per_perm_province'],
                    "per_perm_city" => $req['per_perm_city'],
                    "per_perm_barangay" => $req['per_perm_barangay'],
                    "per_perm_zipcode" => $req['per_perm_zipcode'],
                    "per_regtype" => $req['per_regtype'],
                    "per_updatedby" =>$req['per_user'],
                    "per_dateupdated" => $date,
                ]);
            
            return $data = [
                'status' => 204,
            ];

            

        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
        
       
    }

    public function updatePersonDetails(Request $req, $type)
    {
        switch($type){
            case 1:
                $this->addApplicant($req, 2);
            break;
            case 2:
                $this->addApplicant($req, 3);
            break;
            case 3:
                $this->addApplicant($req, 4);
            break;
        }
 
    }

    public function deleteFamAwrAtt(Request $req, $type){
        try{
            switch($type){
                case 1:
                    $s1 = DB::table('def_person_family')
                        ->where('fam_id','=', $req['id'])
                        ->delete();
                    return $data = [
                        'status' => 204,
                    ];
                break;
                case 2:
                    $s1 = DB::table('def_person_award')
                        ->where('awr_id','=', $req['id'])
                        ->delete();
                    
                    return $data = [
                        'status' => 204,
                    ];
                break;
                case 3:
                    $s1 = DB::table('def_person_attainment')
                        ->where('pered_id','=', $req['id'])
                        ->delete();
                    return $data = [
                        'status' => 204,
                    ];
                break;
            }
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }

    public function deleteApplicant(Request $req)
    {
        try{

            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());

            $s1 = DB::table('def_person')
                ->where('per_id','=', $req['per_id'])
                ->update([
                    "per_status" => 0,
                    "per_updatedby" => $req['per_updatedby'],
                    "per_dateupdated" => $date,
                ]);
            $s2 = DB::table('def_person_family')
                ->where('fam_personid','=', $req['per_id'])
                ->update([
                    "fam_status" => 0,
                ]);
            $s3 = DB::table('def_person_award')
                ->where('awr_personid','=', $req['per_id'])
                ->update([
                    "awr_status" => 0,
                ]);
            $s4 = DB::table('def_person_attainment')
                ->where('att_personid','=', $req['per_id'])
                ->update([
                    "att_status" => 0,
                ]);
            
            return $data = [
                'status' => 204,
            ];
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
        
       
    }

    public function getEnrollment($id)
    {
       
        // $enrollment = DB::table('def_enrollment')
        // ->where('enr_personid', '=' , $id)->get();
        // // ->first();
        // return $enrollment; 

        $enrollment = DB::table('def_enrollment')
            ->leftJoin('def_gradelvl', 'def_enrollment.enr_gradelvl', '=', 'def_gradelvl.grad_id') 
            ->leftJoin('sett_quarter', 'def_enrollment.enr_quarter', '=', 'sett_quarter.quar_id') 
            ->leftJoin('def_program', 'def_enrollment.enr_course', '=', 'def_program.prog_id') 
            ->leftJoin('def_curriculum', 'def_enrollment.enr_curriculum', '=', 'def_curriculum.curr_id') 
            ->leftJoin('def_section', 'def_enrollment.enr_section', '=', 'def_section.sec_id') 

            ->select(  
                'def_enrollment.*',
                'def_gradelvl.*',
                'def_program.*',
                'def_curriculum.*',
                'def_section.*',
                'sett_quarter.*',
            )
            ->orderBy('def_enrollment.enr_dateenrolled','DESC')
            ->where('def_enrollment.enr_personid', '=' , $id)
            ->where('def_enrollment.enr_status', '=' , 1)
            ->get();
        return $enrollment; 
       
    }

    public function enrollApplicant(Request $request)
    {
       try{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());

            $primary = DB::table('def_enrollment')->insert([
                'enr_enrby' => $request->input('userid'),
                'enr_personid' => $request->input('personid'),
                'enr_gradelvl' => $request->input('gradelvl'),
                'enr_program' => $request->input('program'),
                'enr_quarter' => $request->input('quarter'),
                'enr_course' => $request->input('course'),
                'enr_lrn' => $request->input('lrn'),
                'enr_dateenrolled' => $date
            ]);

            if($primary){
                $enr = DB::table('def_enrollment')
                ->where('enr_personid', '=' ,  $request->input('personid'))
                ->where('enr_status', '=' ,  1)
                ->first();

                $primary = DB::table('def_accounts_settlement')->insert([
                    'acs_personid' => $request->input('personid'),
                    'acs_enrid' => $enr->enr_id,
                    'acs_dateadded' => $date
                ]);
            }

            return 204;
        }
        catch (Exception $ex) {
            return 500;
        }
    }

    public function getStudent($limit, $offset, $search)
    {
        if($search==204){

            if($limit == 0 && $offset == 0){
                $student = DB::table('def_enrollment')
                ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                ->select(  
                    'def_enrollment.*',
                    'def_person.*',
                )->orderBy('def_person.per_id','DESC')
                ->where('def_enrollment.enr_status', '=' , 1)
                ->get();
            }else{
                $student = DB::table('def_enrollment')
                ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                ->select(  
                    'def_enrollment.*',
                    'def_person.*',
                )->orderBy('def_person.per_id','DESC')
                ->where('def_enrollment.enr_status', '=' , 1)
                ->limit($limit)
                ->offset($offset)
                ->get();
            }
           
            $count = DB::table('def_enrollment')
            ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
            ->select(  
                'def_enrollment.*',
                'def_person.*',
            )->where('def_enrollment.enr_status', '=' , 1)
            ->count();
        }
        else if ($search != 204 && ($limit == 1 && $offset == 1)){
            $student = DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )->orderBy('def_person.per_id','DESC')
                        ->where('def_person.per_status', '=',  1)
                        ->where('def_enrollment.enr_status', '=' , 1)
                        ->where(function($query) use ($search) {
                            $query->where('per_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('per_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('per_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('per_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('per_dateapplied', 'like',  '%' . $search .'%');
                        })
                        ->get();
            $count =  DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )->orderBy('def_person.per_id','DESC')
                        ->where('def_person.per_status', '=',  1)
                        ->where('def_enrollment.enr_status', '=' , 1)
                        ->where(function($query) use ($search) {
                            $query->where('per_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('per_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('per_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('per_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('per_dateapplied', 'like',  '%' . $search .'%');
                        })
                        ->count();       
        }
        else{
             $student = DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )->orderBy('def_person.per_id','DESC')
                        ->where('def_person.per_status', '=',  1)
                        ->where('def_enrollment.enr_status', '=' , 1)
                        ->where(function($query) use ($search) {
                            $query->where('per_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('per_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('per_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('per_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('per_dateapplied', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )->orderBy('def_person.per_id','DESC')
                        ->where('def_person.per_status', '=',  1)
                        ->where('def_enrollment.enr_status', '=' , 1)
                        ->where(function($query) use ($search) {
                            $query->where('per_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('per_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('per_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('per_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('per_dateapplied', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $student,
            'count' => $count,
        ];
    }

    public function getStudentFiltering($limit, $offset, $fname, $mname, $lname, $program, $gradelvl, $course)
    {   
        // kapag newly refresh no params for search
        if(($fname == 404)&&($mname == 404)&&($lname == 404)&&($program == 0)&&($gradelvl == 0)&&($course == 0)){

            if($limit == 0 && $offset == 0){
                $student = DB::table('def_enrollment')
                ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                ->select(  
                    'def_enrollment.*',
                    'def_person.*',
                )
                ->orderBy('def_enrollment.enr_course')
                ->orderByDesc('def_enrollment.enr_dateenrolled')
                ->where('def_enrollment.enr_status', '=' , 1)
                ->get();
            }else{
                $student = DB::table('def_enrollment')
                ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                ->select(  
                    'def_enrollment.*',
                    'def_person.*',
                )
                ->orderBy('def_enrollment.enr_dateenrolled')
                ->orderByDesc('def_enrollment.enr_dateenrolled')
                ->where('def_enrollment.enr_status', '=' , 1)
                ->limit($limit)
                ->offset($offset)
                ->get();
            }
           
            $count = DB::table('def_enrollment')
            ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
            ->select(  
                'def_enrollment.*',
                'def_person.*',
            )                        
            ->orderBy('def_enrollment.enr_course')
            ->orderByDesc('def_enrollment.enr_dateenrolled')
            ->where('def_enrollment.enr_status', '=' , 1)
            ->count();
        }
        // kapag may params for search
        // ($fname != 404)||($mname != 404)||($lname != 404) && ($limit == 1 && $offset == 1)
        else if (($fname == 404)&&($mname == 404)&&($lname == 404)){
            $student = DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )                        
                        ->orderBy('def_enrollment.enr_course')
                        ->orderByDesc('def_enrollment.enr_dateenrolled')
                        ->where('def_person.per_status', '=',  1)
                        ->where('def_enrollment.enr_status', '=' , 1)
                        ->orWhere('def_enrollment.enr_gradelvl', '=' , $gradelvl)
                        ->orWhere('def_enrollment.enr_program', '=' , $program)
                        ->orWhere('def_enrollment.enr_course', '=' , $course)
                        ->get();
            $count =  DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )                        
                        ->orderBy('def_enrollment.enr_course')
                        ->orderByDesc('def_enrollment.enr_dateenrolled')
                        ->where('def_person.per_status', '=',  1)
                        ->where('def_enrollment.enr_status', '=' , 1)
                        ->orWhere('def_enrollment.enr_gradelvl', '=' , $gradelvl)
                        ->orWhere('def_enrollment.enr_program', '=' , $program)
                        ->orWhere('def_enrollment.enr_course', '=' , $course)
                        ->count();       
        }
        else{
             $student = DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )                        
                        ->where('def_person.per_status', '=',  1)
                        ->where('def_enrollment.enr_status', '=' , 1)
                        ->where(function($query) use ($fname, $mname, $lname) {
                            $query->orWhere('def_person.per_firstname', 'like',  '%' . $fname .'%')
                            ->orWhere('def_person.per_middlename', 'like',  '%' . $mname .'%')
                            ->orWhere('def_person.per_lastname', 'like',  '%' . $lname .'%');
                        })
                        // ->where(function($query) use ($program, $gradelvl, $course) {
                        //     $query->orWhere('def_enrollment.enr_gradelvl', '=', $gradelvl)
                        //     ->orWhere('def_enrollment.enr_program', '=',  $program)
                        //     ->orWhere('def_enrollment.enr_course', '=',  $course);
                        // })
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )
                        ->where('def_person.per_status', '=',  1)
                        ->where('def_enrollment.enr_status', '=' , 1)
                        ->where(function($query) use ($fname, $mname, $lname) {
                            $query->orWhere('def_person.per_firstname', 'like',  '%' . $fname .'%')
                            ->orWhere('def_person.per_middlename', 'like',  '%' . $mname .'%')
                            ->orWhere('def_person.per_lastname', 'like',  '%' . $lname .'%');
                        })
                        // ->where(function($query) use ($program, $gradelvl, $course) {
                        //     $query->orWhere('def_enrollment.enr_gradelvl', '=', $gradelvl)
                        //     ->orWhere('def_enrollment.enr_program', '=',  $program)
                        //     ->orWhere('def_enrollment.enr_course', '=',  $course);
                        // })
                        ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $student,
            'count' => $count,
        ];
    }

    public function getStudentByCourse($limit, $offset, $search)
    {
        if($search==204){

            $student = DB::table('def_enrollment')
            ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
            ->select(  
                'def_enrollment.*',
                'def_person.*',
            )->orderBy('def_person.per_id','DESC')
            ->limit($limit)
            ->offset($offset)
            ->get();
                            

            $count = DB::table('def_enrollment')
            ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
            ->select(  
                'def_enrollment.*',
                'def_person.*',
            )->count();
        }
        else{
             $student = DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )->orderBy('def_person.per_id','DESC')
                        ->where('per_status', '=',  1)
                        ->where('enr_course', '=',  $search)
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_enrollment')
                        ->leftJoin('def_person', 'def_enrollment.enr_personid', '=', 'def_person.per_id') 
                        ->select(  
                            'def_enrollment.*',
                            'def_person.*',
                        )->orderBy('def_person.per_id','DESC')
                        ->where('per_status', '=',  1)
                        ->where('enr_course', '=',  $search)
                        ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $student,
            'count' => $count,
        ];
    }

    public function addMilestone(Request $request)
    {
        
        // date_default_timezone_set('Asia/Manila');
        // $date = date('Y-m-d h:i:s', time());
        // $primary = DB::table('def_milestone')->insert([
        //     'mi_enrid' =>  $request->input('enr_id'),
        //     'mi_subjid' =>  $request->input('subj_id'),
        //     'mi_completed' =>  0,
        //     'mi_date' =>  $date,
        //     'mi_addby' => $request->input('user_id')
        // ]);
        $milestone = DB::table('def_milestone')
            ->select('mi_id')
            ->where('mi_enrid', '=' , $request->input('enr_id'))
            ->where('mi_subjid', '=' , $request->input('subj_id'))
            ->first();

        if($milestone){
            $milestoneid = $milestone->mi_id;

            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $primary = DB::table('def_milestone')
            ->where('mi_id','=', $milestoneid)
            ->update([
                'mi_updatedby' => $request->input('user_id'),
                'mi_crossenr' => $request->input('mi_crossenr'),
                'mi_tag' => $request->input('mi_tag'),
                'mi_status' => 1,
                'mi_dateupdated' => $date,
            ]);

        }else{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $primary = DB::table('def_milestone')->insert([
                'mi_enrid' =>  $request->input('enr_id'),
                'mi_subjid' =>  $request->input('subj_id'),
                'mi_crossenr' =>  $request->input('crossenr'),
                'mi_tag' =>  $request->input('mi_tag'),
                'mi_completed' =>  0,
                'mi_date' =>  $date,
                'mi_addby' => $request->input('user_id')
            ]);
        }
        return 200; 
       
    }

    public function getMilestone($id)
    {
        $milestone = DB::table('def_milestone as a')
            ->leftJoin('def_enrollment as b', 'a.mi_enrid', '=', 'b.enr_id') 
            ->leftJoin('def_subject as c', 'a.mi_subjid', '=', 'c.subj_id') 
            ->leftJoin('def_subject as d', 'c.subj_preq', '=', 'd.subj_id') 
            ->select(  
                'b.*',
                'a.*',
                'c.*',
                'd.subj_code as subj_preq_code',
            )
            ->orderBy('b.enr_dateenrolled','ASC')
            ->where('a.mi_enrid', '=' , $id)
            ->where('a.mi_status', '=' , 1)
            ->get();
        return $milestone; 
       
    }
    
    public function updateEnrollment(Request $request){
        try{
            
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $primary = DB::table('def_enrollment')
            ->where('enr_id','=', $request->input('enr_id'))
            ->update([
                'enr_curriculum' => $request->input('enr_curriculum'),
                'enr_section' => $request->input('enr_section'),
                'enr_updatedby' => $request->input('enr_updatedby'),
                'enr_dateupdated' => $date,
            ]);
            return 204;
        }
        catch (Exception $ex) {
            return 500;
        }
    }

    public function updateMilestone(Request $request)
    {
        try{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $primary = DB::table('def_milestone')
            ->where('mi_id','=', $request->input('mi_id'))
            ->update([
                'mi_status' => $request->input('mi_status'),
                'mi_updatedby' => $request->input('mi_updatedby'),
                'mi_dateupdated' => $date,
            ]);
            return 204;
        }
        catch (Exception $ex) {
            return 500;
        }
    }

    public function getCurriculum($limit, $offset, $search)
    {
        if($search == 204){
            $curriculum = DB::table('def_curriculum')->orderBy('curr_id')
            ->where('curr_status', '=' , 1)
            ->get();
            return $curriculum;
        }else{
            $curriculum = DB::table('def_curriculum')
            ->select(  
                'def_curriculum.*',
            )->orderBy('curr_id','DESC')
            ->where('curr_status', '=',  1)
            ->where(function($query) use ($search) {
                $query->where('curr_code', 'like',  '%' . $search .'%')
                ->orWhere('curr_from', 'like',  '%' . $search .'%')
                ->orWhere('curr_to', 'like',  '%' . $search .'%')
                ->orWhere('curr_progid', 'like',  '%' . $search .'%')
                ->orWhere('curr_progtype', 'like',  '%' . $search .'%')
                ->orWhere('curr_tagid', 'like',  '%' . $search .'%');
            })
            ->limit($limit)->offset($offset)
            ->get();
            return $curriculum;
        }
    }

    public function getCurriculumStudent($prog, $type)
    {
        
        $curriculum = DB::table('def_curriculum')->orderBy('curr_id')
        ->where('curr_progid', '=' , $prog)
        ->where('curr_progtype', '=' , $type)
        ->get();
        return $curriculum; 
       
    }

    public function getCurriculumSubject($curr, $sem, $gradelvl)
    {
       if(($sem == 0) && ($gradelvl == 0)){
            $curriculum = DB::table('def_curriculum_tags')
            ->leftJoin('def_subject', 'def_curriculum_tags.currtag_subjid', '=', 'def_subject.subj_id') 
            ->select(  
                'def_subject.*',
                'def_curriculum_tags.*',
            )->orderBy('def_curriculum_tags.currtag_id')
            ->where('def_curriculum_tags.currtag_status', '=' ,1)
            ->where('def_curriculum_tags.currtag_currid', '=' , $curr)
            ->get();
            return $curriculum;
       }else{
            $curriculum = DB::table('def_curriculum_tags')
            ->leftJoin('def_subject', 'def_curriculum_tags.currtag_subjid', '=', 'def_subject.subj_id') 
            ->select(  
                'def_subject.*',
                'def_curriculum_tags.*',
            )->orderBy('def_curriculum_tags.currtag_id')
            ->where('def_curriculum_tags.currtag_status', '=' ,1)
            ->where('def_curriculum_tags.currtag_sem', '=' ,$sem)
            ->where('def_curriculum_tags.currtag_gradelvl', '=' ,$gradelvl)
            ->where('def_curriculum_tags.currtag_currid', '=' , $curr)
            ->get();
            return $curriculum;
       }
       
    }
    public function getlaunchChecker(Request $params)
    {
        $launch = DB::table('def_launch')
        ->select(  
            'def_launch.*'
        )
        ->where('ln_status', '=',  1)
        ->where('ln_dtype', '=',  $params->ln_dtype)
        ->where('ln_quarter', '=',  $params->ln_quarter)
        ->where('ln_course', '=',  $params->ln_course)
        ->where('ln_gradelvl', '=',  $params->ln_gradelvl)
        ->where('ln_curriculum', '=',  $params->ln_curriculum)
        ->where('ln_section', '=',  $params->ln_section)
        ->where('ln_year', '=',  $params->ln_year)
        ->first();

        return $launch;
    }

    public function getlaunch($limit, $offset, $search)
    {
        if($search==204){

            $launch = DB::table('def_launch')
            ->leftJoin('def_program', 'def_launch.ln_course', '=', 'def_program.prog_id') 
            ->leftJoin('def_section', 'def_launch.ln_section', '=', 'def_section.sec_id')
            ->leftJoin('def_curriculum', 'def_launch.ln_curriculum', '=', 'def_curriculum.curr_id')
            ->leftJoin('def_gradelvl', 'def_launch.ln_gradelvl', '=', 'def_gradelvl.grad_id')
            ->leftJoin('sett_quarter', 'def_launch.ln_quarter', '=', 'sett_quarter.quar_id')
            ->leftJoin('sett_degree_types', 'def_launch.ln_dtype', '=', 'sett_degree_types.dtype_id')
            ->select(  
                'def_launch.*',
                'def_program.prog_id',
                'def_program.prog_code',
                'def_program.prog_name',
                'def_section.sec_id',
                'def_section.sec_name',
                'def_curriculum.curr_id',
                'def_curriculum.curr_code',
                'def_gradelvl.grad_id',
                'def_gradelvl.grad_code',
                'def_gradelvl.grad_name',
                'def_gradelvl.grad_dtypeid',
                'sett_quarter.quar_id',
                'sett_quarter.quar_desc',
                'sett_quarter.quar_code',
                'sett_degree_types.dtype_id',
                'sett_degree_types.dtype_desc',

            )->orderBy('def_launch.ln_id','DESC')
            ->limit($limit)
            ->offset($offset)
            ->get();
                            

            $count = DB::table('def_launch')
            ->leftJoin('def_program', 'def_launch.ln_course', '=', 'def_program.prog_id') 
            ->leftJoin('def_section', 'def_launch.ln_section', '=', 'def_section.sec_id')
            ->leftJoin('def_curriculum', 'def_launch.ln_curriculum', '=', 'def_curriculum.curr_id')
            ->leftJoin('def_gradelvl', 'def_launch.ln_gradelvl', '=', 'def_gradelvl.grad_id')
            ->leftJoin('sett_quarter', 'def_launch.ln_quarter', '=', 'sett_quarter.quar_id')
            ->leftJoin('sett_degree_types', 'def_launch.ln_dtype', '=', 'sett_degree_types.dtype_id')
            ->select(  
                'def_launch.*',
            )->count();
        }
        else{
             $launch = DB::table('def_launch')
                        ->leftJoin('def_program', 'def_launch.ln_course', '=', 'def_program.prog_id') 
                        ->leftJoin('def_section', 'def_launch.ln_section', '=', 'def_section.sec_id')
                        ->leftJoin('def_curriculum', 'def_launch.ln_curriculum', '=', 'def_curriculum.curr_id')
                        ->leftJoin('def_gradelvl', 'def_launch.ln_gradelvl', '=', 'def_gradelvl.grad_id')
                        ->leftJoin('sett_quarter', 'def_launch.ln_quarter', '=', 'sett_quarter.quar_id')
                        ->leftJoin('sett_degree_types', 'def_launch.ln_dtype', '=', 'sett_degree_types.dtype_id')
                        ->select(  
                            'def_launch.*',
                            'def_program.prog_id',
                            'def_program.prog_code',
                            'def_program.prog_name',
                            'def_section.sec_id',
                            'def_section.sec_name',
                            'def_curriculum.curr_id',
                            'def_curriculum.curr_code',
                            'def_gradelvl.grad_id',
                            'def_gradelvl.grad_code',
                            'def_gradelvl.grad_name',
                            'def_gradelvl.grad_dtypeid',
                            'sett_quarter.quar_id',
                            'sett_quarter.quar_desc',
                            'sett_quarter.quar_code',
                            'sett_degree_types.dtype_id',
                            'sett_degree_types.dtype_desc',
            
                        )->orderBy('def_launch.ln_id','DESC')
                        ->where('ln_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('def_launch.ln_year', 'like',  '%' . $search .'%')
                            ->orWhere('def_launch.ln_slots', 'like',  '%' . $search .'%')
                            ->orWhere('def_program.prog_name', 'like',  '%' . $search .'%')
                            ->orWhere('sett_quarter.quar_desc', 'like',  '%' . $search .'%')
                            ->orWhere('sett_degree_types.dtype_desc', 'like',  '%' . $search .'%')
                            ->orWhere('def_section.sec_name', 'like',  '%' . $search .'%')
                            ->orWhere('def_gradelvl.grad_name', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_launch')
                        ->leftJoin('def_program', 'def_launch.ln_course', '=', 'def_program.prog_id') 
                        ->leftJoin('def_section', 'def_launch.ln_section', '=', 'def_section.sec_id')
                        ->leftJoin('def_curriculum', 'def_launch.ln_curriculum', '=', 'def_curriculum.curr_id')
                        ->leftJoin('def_gradelvl', 'def_launch.ln_gradelvl', '=', 'def_gradelvl.grad_id')
                        ->leftJoin('sett_quarter', 'def_launch.ln_quarter', '=', 'sett_quarter.quar_id')
                        ->leftJoin('sett_degree_types', 'def_launch.ln_dtype', '=', 'sett_degree_types.dtype_id')
                        ->select(  
                            'def_launch.*'
                        )->orderBy('def_launch.ln_id','DESC')
                        ->where('ln_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('def_launch.ln_year', 'like',  '%' . $search .'%')
                            ->orWhere('def_launch.ln_slots', 'like',  '%' . $search .'%')
                            ->orWhere('def_program.prog_name', 'like',  '%' . $search .'%')
                            ->orWhere('sett_quarter.quar_desc', 'like',  '%' . $search .'%')
                            ->orWhere('sett_degree_types.dtype_desc', 'like',  '%' . $search .'%')
                            ->orWhere('def_section.sec_name', 'like',  '%' . $search .'%')
                            ->orWhere('def_gradelvl.grad_name', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $launch,
            'count' => $count,
        ];
    }

    public function addLaunch(Request $request)
    {
       try{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());

            $primary = DB::table('def_launch')->insert([
                'ln_dtype' => $request->input('ln_dtype'),
                'ln_quarter' => $request->input('ln_quarter'),
                'ln_course' => $request->input('ln_course'),
                'ln_gradelvl' => $request->input('ln_gradelvl'),
                'ln_curriculum' => $request->input('ln_curriculum'),
                'ln_section' => $request->input('ln_section'),
                'ln_slots' => $request->input('ln_slots'),
                'ln_year' => $request->input('ln_year'),
                'ln_addedby' => $request->input('ln_addedby'),
                'ln_dateadded' => $date
            ]);
            return 204;
        }
        catch (Exception $ex) {
            return 500;
        }
    }

    public function addSchedule(Request $request)
    {
       date_default_timezone_set('Asia/Manila');
       $date = date('Y-m-d h:i:s', time());

       try{
            if($request->input('sched_id')){
                $s1 = DB::table('def_launch_schedule')
                    ->where('sched_id','=', $request['sched_id'])
                    ->update([
                        "sched_time" => $request['sched_time'],
                        
                        "sched_mon" => $request['sched_mon'],
                        "sched_mon_code" => $request['sched_mon_code'],
                        "sched_mon_bid" => $request['sched_mon_bid'],
                        "sched_mon_classrid" => $request['sched_mon_classrid'],

                        "sched_tue" => $request['sched_tue'],
                        "sched_tue_code" => $request['sched_tue_code'],
                        "sched_tue_bid" => $request['sched_tue_bid'],
                        "sched_tue_classrid" => $request['sched_tue_classrid'],

                        "sched_wed" => $request['sched_wed'],
                        "sched_wed_code" => $request['sched_wed_code'],
                        "sched_wed_bid" => $request['sched_wed_bid'],
                        "sched_wed_classrid" => $request['sched_wed_classrid'],

                        "sched_thurs" => $request['sched_thurs'],
                        "sched_thurs_code" => $request['sched_thurs_code'],
                        "sched_thurs_bid" => $request['sched_thurs_bid'],
                        "sched_thurs_classrid" => $request['sched_thurs_classrid'],

                        "sched_fri" => $request['sched_fri'],
                        "sched_fri_code" => $request['sched_fri_code'],
                        "sched_fri_bid" => $request['sched_fri_bid'],
                        "sched_fri_classrid" => $request['sched_fri_classrid'],

                        "sched_sat" => $request['sched_sat'],
                        "sched_sat_code" => $request['sched_sat_code'],
                        "sched_sat_bid" => $request['sched_sat_bid'],
                        "sched_sat_classrid" => $request['sched_sat_classrid'],

                        "sched_lnid" => $request['sched_lnid'],
                ]);

                if(empty($request['sched_mon'])){
                    if($request['sched_mon_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_mon_schedid" => null
                        ]);
                        // return 'yey nag update amputa';

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_mon_faculty_occid'])
                        ->delete();    
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_mon_bid','=', $request['sched_mon_bid'])
                        ->where('occ_mon_classrid','=', $request['sched_mon_classrid'])
                        ->update([
                            "occ_mon_schedid" =>  $request->input('sched_id')
                        ]);

                    if($request['sched_mon_faculty_occid']){
                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_mon_faculty_occid'])
                        ->update([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_mon_faculty'),
                            "occ_subjid" =>  $request->input('sched_mon'),
                        ]);
                    }else{
                        $s3 = DB::table('def_launch_occupancy_faculty')
                        ->insert([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_mon_faculty'),
                            "occ_subjid" =>  $request->input('sched_mon'),
                            "occ_day" =>  'Monday',
                        ]); 
                    }
 
                }

                if(empty($request['sched_tue'])){
                    if($request['sched_tue_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_tue_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_tue_faculty_occid'])
                        ->delete();    
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_tue_bid','=', $request['sched_tue_bid'])
                        ->where('occ_tue_classrid','=', $request['sched_tue_classrid'])
                        ->update([
                            "occ_tue_schedid" =>  $request->input('sched_id')
                        ]);

                    if($request['sched_tue_faculty_occid']){
                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_tue_faculty_occid'])
                        ->update([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_tue_faculty'),
                            "occ_subjid" =>  $request->input('sched_tue'),
                        ]);   
                    }else{
                        $s3 = DB::table('def_launch_occupancy_faculty')
                        ->insert([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_tue_faculty'),
                            "occ_subjid" =>  $request->input('sched_tue'),
                            "occ_day" =>  'Tuesday',
                        ]); 
                    }
                }

                if(empty($request['sched_wed'])){
                    if($request['sched_wed_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_wed_schedid" => null,
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_wed_faculty_occid'])
                        ->delete();      
                    }
                   
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_wed_bid','=', $request['sched_wed_bid'])
                        ->where('occ_wed_classrid','=', $request['sched_wed_classrid'])
                        ->update([
                            "occ_wed_schedid" =>  $request->input('sched_id')
                        ]);
                    
                    if($request['sched_wed_faculty_occid']){
                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_wed_faculty_occid'])
                        ->update([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_wed_faculty'),
                            "occ_subjid" =>  $request->input('sched_wed'),
                        ]);
                    }else{
                        $s3 = DB::table('def_launch_occupancy_faculty')
                        ->insert([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_wed_faculty'),
                            "occ_subjid" =>  $request->input('sched_wed'),
                            "occ_day" =>  'Wednesday',
                        ]); 
                    }
                     
                }

                if(empty($request['sched_thurs'])){
                    if($request['sched_thurs_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_thurs_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_thurs_faculty_occid'])
                        ->delete();       
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_thurs_bid','=', $request['sched_thurs_bid'])
                        ->where('occ_thurs_classrid','=', $request['sched_thurs_classrid'])
                        ->update([
                            "occ_thurs_schedid" =>  $request->input('sched_id')
                        ]);
                    
                    if($request['sched_thurs_faculty_occid']){
                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_thurs_faculty_occid'])
                        ->update([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_thurs_faculty'),
                            "occ_subjid" =>  $request->input('sched_thurs'),
                        ]);
                    }else{
                        $s3 = DB::table('def_launch_occupancy_faculty')
                        ->insert([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_thurs_faculty'),
                            "occ_subjid" =>  $request->input('sched_thurs'),
                            "occ_day" =>  'Thursday',
                        ]);
                    }

                     
                }

                if(empty($request['sched_fri'])){
                    if($request['sched_fri_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_fri_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_fri_faculty_occid'])
                        ->delete();        
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_fri_bid','=', $request['sched_fri_bid'])
                        ->where('occ_fri_classrid','=', $request['sched_fri_classrid'])
                        ->update([
                            "occ_fri_schedid" =>  $request->input('sched_id')
                        ]);
                    
                    if($request['sched_fri_faculty_occid']){
                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_fri_faculty_occid'])
                        ->update([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_fri_faculty'),
                            "occ_subjid" =>  $request->input('sched_fri'),
                        ]);
                    }else{
                        $s3 = DB::table('def_launch_occupancy_faculty')
                        ->insert([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_fri_faculty'),
                            "occ_subjid" =>  $request->input('sched_fri'),
                            "occ_day" =>  'Friday',
                        ]);
                    }
                             
                }

                if(empty($request['sched_sat'])){
                    if($request['sched_sat_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_sat_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_sat_faculty_occid'])
                        ->delete();        
                    }
                
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_sat_bid','=', $request['sched_sat_bid'])
                        ->where('occ_sat_classrid','=', $request['sched_sat_classrid'])
                        ->update([
                            "occ_sat_schedid" =>  $request->input('sched_id')
                        ]);
                    
                    if($request['sched_sat_faculty_occid']){
                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_sat_faculty_occid'])
                        ->update([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_sat_faculty'),
                            "occ_subjid" =>  $request->input('sched_sat'),
                        ]);
                    }else{
                        $s3 = DB::table('def_launch_occupancy_faculty')
                        ->insert([
                            "occ_time" =>  $request->input('sched_time'),
                            "occ_lnid" =>  $request->input('sched_lnid'),
                            "occ_faculty" =>  $request->input('sched_sat_faculty'),
                            "occ_subjid" =>  $request->input('sched_sat'),
                            "occ_day" =>  'Saturday',
                        ]);
                    }
                                 
                }

            }
            else{
                $s1 = DB::table('def_launch_schedule')->insert([
                    'sched_lnid' => $request->input('sched_lnid'),
                    'sched_time' => $request->input('sched_time'),

                    'sched_mon' => $request->input('sched_mon'),
                    'sched_mon_code' => $request->input('sched_mon_code'),
                    'sched_mon_bid' => $request->input('sched_mon_bid'),
                    'sched_mon_classrid' => $request->input('sched_mon_classrid'),

                    'sched_tue' => $request->input('sched_tue'),
                    'sched_tue_code' => $request->input('sched_tue_code'),
                    'sched_tue_bid' => $request->input('sched_tue_bid'),
                    'sched_tue_classrid' => $request->input('sched_tue_classrid'),

                    'sched_wed' => $request->input('sched_wed'),
                    'sched_wed_code' => $request->input('sched_wed_code'),
                    'sched_wed_bid' => $request->input('sched_wed_bid'),
                    'sched_wed_classrid' => $request->input('sched_wed_classrid'),

                    'sched_thurs' => $request->input('sched_thurs'),
                    'sched_thurs_code' => $request->input('sched_thurs_code'),
                    'sched_thurs_bid' => $request->input('sched_thurs_bid'),
                    'sched_thurs_classrid' => $request->input('sched_thurs_classrid'),

                    'sched_fri' => $request->input('sched_fri'),
                    'sched_fri_code' => $request->input('sched_fri_code'),
                    'sched_fri_bid' => $request->input('sched_fri_bid'),
                    'sched_fri_classrid' => $request->input('sched_fri_classrid'),

                    'sched_sat' => $request->input('sched_sat'),
                    'sched_sat_code' => $request->input('sched_sat_code'),
                    'sched_sat_bid' => $request->input('sched_sat_bid'),
                    'sched_sat_classrid' => $request->input('sched_sat_classrid'),

                    
                    'sched_addedby' => $request->input('sched_addedby'),
                    'sched_dateadded' => $date
                ]);

                $schedid = DB::table('def_launch_schedule')
                    ->select('sched_id')
                    ->where('sched_lnid', '=',  $request->input('sched_lnid'))
                    ->where('sched_time', '=',  $request->input('sched_time'))
                    ->where('sched_status', '=', 1)
                    ->first();

                
                if(empty($request['sched_mon'])){
                    if($request['sched_mon_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_mon_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_mon_faculty_occid'])
                        ->delete();        
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_mon_bid','=', $request['sched_mon_bid'])
                        ->where('occ_mon_classrid','=', $request['sched_mon_classrid'])
                        ->update([
                            "occ_mon_schedid" =>  $schedid->sched_id
                        ]);
                    
                    $s2 = DB::table('def_launch_occupancy_faculty')
                    ->insert([
                        "occ_time" =>  $request->input('sched_time'),
                        "occ_lnid" =>  $request->input('sched_lnid'),
                        "occ_faculty" =>  $request->input('sched_mon_faculty'),
                        "occ_subjid" =>  $request->input('sched_mon'),
                        "occ_day" =>  'Monday',
                    ]);       
                }

                if(empty($request['sched_tue'])){
                    if($request['sched_tue_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_tue_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_tue_faculty_occid'])
                        ->delete();        
                    }

                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_tue_bid','=', $request['sched_tue_bid'])
                        ->where('occ_tue_classrid','=', $request['sched_tue_classrid'])
                        ->update([
                            "occ_tue_schedid" =>  $schedid->sched_id
                        ]);
                    
                    $s2 = DB::table('def_launch_occupancy_faculty')
                    ->insert([
                        "occ_time" =>  $request->input('sched_time'),
                        "occ_lnid" =>  $request->input('sched_lnid'),
                        "occ_faculty" =>  $request->input('sched_tue_faculty'),
                        "occ_subjid" =>  $request->input('sched_tue'),
                        "occ_day" =>  'Tuesday',
                    ]);        
                }

                if(empty($request['sched_wed'])){
                    if($request['sched_wed_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_wed_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_wed_faculty_occid'])
                        ->delete();        
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_wed_bid','=', $request['sched_wed_bid'])
                        ->where('occ_wed_classrid','=', $request['sched_wed_classrid'])
                        ->update([
                            "occ_wed_schedid" =>  $schedid->sched_id
                        ]);

                    $s2 = DB::table('def_launch_occupancy_faculty')
                    ->insert([
                        "occ_time" =>  $request->input('sched_time'),
                        "occ_lnid" =>  $request->input('sched_lnid'),
                        "occ_faculty" =>  $request->input('sched_wed_faculty'),
                        "occ_subjid" =>  $request->input('sched_wed'),
                        "occ_day" =>  'Wednesday',
                    ]);       
                }

                if(empty($request['sched_thurs'])){
                    if($request['sched_thurs_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_thurs_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_thurs_faculty_occid'])
                        ->delete();        
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_thurs_bid','=', $request['sched_thurs_bid'])
                        ->where('occ_thurs_classrid','=', $request['sched_thurs_classrid'])
                        ->update([
                            "occ_thurs_schedid" =>  $schedid->sched_id
                        ]);
                        
                    $s2 = DB::table('def_launch_occupancy_faculty')
                    ->insert([
                        "occ_time" =>  $request->input('sched_time'),
                        "occ_lnid" =>  $request->input('sched_lnid'),
                        "occ_faculty" =>  $request->input('sched_thurs_faculty'),
                        "occ_subjid" =>  $request->input('sched_thurs'),
                        "occ_day" =>  'Thursday',
                    ]);        
                }

                if(empty($request['sched_fri'])){
                    if($request['sched_fri_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_fri_schedid" => null,
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_fri_faculty_occid'])
                        ->delete();        
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_fri_bid','=', $request['sched_fri_bid'])
                        ->where('occ_fri_classrid','=', $request['sched_fri_classrid'])
                        ->update([
                            "occ_fri_schedid" =>  $schedid->sched_id
                        ]);
                    
                    $s2 = DB::table('def_launch_occupancy_faculty')
                    ->insert([
                        "occ_time" =>  $request->input('sched_time'),
                        "occ_lnid" =>  $request->input('sched_lnid'),
                        "occ_faculty" =>  $request->input('sched_fri_faculty'),
                        "occ_subjid" =>  $request->input('sched_fri'),
                        "occ_day" =>  'Friday',
                    ]);       
                }

                if(empty($request['sched_sat'])){
                    if($request['sched_sat_remove'] == true){
                        $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->update([
                            "occ_sat_schedid" => null
                        ]);

                        $s2 = DB::table('def_launch_occupancy_faculty')
                        ->where('occ_id','=', $request['sched_sat_faculty_occid'])
                        ->delete();       
                    }
                    
                }else{
                    $s1 = DB::table('def_launch_occupancy_subjects')
                        ->where('occ_time','=', $request['sched_time'])
                        ->where('occ_sat_bid','=', $request['sched_sat_bid'])
                        ->where('occ_sat_classrid','=', $request['sched_sat_classrid'])
                        ->update([
                            "occ_sat_schedid" =>  $schedid->sched_id
                        ]);

                    $s2 = DB::table('def_launch_occupancy_faculty')
                    ->insert([
                        "occ_time" =>  $request->input('sched_time'),
                        "occ_lnid" =>  $request->input('sched_lnid'),
                        "occ_faculty" =>  $request->input('sched_sat_faculty'),
                        "occ_subjid" =>  $request->input('sched_sat'),
                        "occ_day" =>  'Saturday',
                    ]);        
                }
            }

        }
        
        catch (Exception $ex) {
            return 500;
        }
    }

    public function getSchedule($launchid)
    {
       if(empty($launchid)){
            $schedule = DB::table('def_launch_schedule')
                    ->orderBy('sched_id')
                    ->where('sched_status', '=', 1)
                    ->where('sched_ended', '=', 0)
                    ->get();
            return $schedule; 
       }else{
            $schedule = DB::table('def_launch_schedule')
                    ->orderBy('sched_id')
                    ->where('sched_lnid', '=', $launchid)
                    ->where('sched_status', '=', 1)
                    ->where('sched_ended', '=', 0)
                    ->get();
            return $schedule; 
       }
    }
    

    public function getOccupancyOthers($othersid)
    {

            return $othersid;

    }

    public function getOccupancy($bid, $classrid)
    {
      
            $occupancy = DB::table('def_launch_occupancy_subjects as sched_occ')
            ->leftJoin('def_launch_schedule as mon_sched', 'sched_occ.occ_mon_schedid', '=', 'mon_sched.sched_id') 
            ->leftJoin('def_launch_schedule as tue_sched', 'sched_occ.occ_tue_schedid', '=', 'tue_sched.sched_id') 
            ->leftJoin('def_launch_schedule as wed_sched', 'sched_occ.occ_wed_schedid', '=', 'wed_sched.sched_id') 
            ->leftJoin('def_launch_schedule as thurs_sched', 'sched_occ.occ_thurs_schedid', '=', 'thurs_sched.sched_id') 
            ->leftJoin('def_launch_schedule as fri_sched', 'sched_occ.occ_fri_schedid', '=', 'fri_sched.sched_id') 
            ->leftJoin('def_launch_schedule as sat_sched', 'sched_occ.occ_sat_schedid', '=', 'sat_sched.sched_id') 
    
            ->leftJoin('def_launch as mon_launch', 'mon_sched.sched_lnid', '=', 'mon_launch.ln_id') 
            ->leftJoin('def_section as mon_section', 'mon_launch.ln_section', '=', 'mon_section.sec_id') 
            ->leftJoin('def_gradelvl as mon_gradelvl', 'mon_launch.ln_gradelvl', '=', 'mon_gradelvl.grad_id') 
            ->leftJoin('def_subject as mon_subject', 'mon_sched.sched_mon', '=', 'mon_subject.subj_id') 
    
            ->leftJoin('def_launch as tue_launch', 'tue_sched.sched_lnid', '=', 'tue_launch.ln_id') 
            ->leftJoin('def_section as tue_section', 'tue_launch.ln_section', '=', 'tue_section.sec_id') 
            ->leftJoin('def_gradelvl as tue_gradelvl', 'tue_launch.ln_gradelvl', '=', 'tue_gradelvl.grad_id') 
            ->leftJoin('def_subject as tue_subject', 'tue_sched.sched_tue', '=', 'tue_subject.subj_id') 
    
            ->leftJoin('def_launch as wed_launch', 'wed_sched.sched_lnid', '=', 'wed_launch.ln_id') 
            ->leftJoin('def_section as wed_section', 'wed_launch.ln_section', '=', 'wed_section.sec_id') 
            ->leftJoin('def_gradelvl as wed_gradelvl', 'wed_launch.ln_gradelvl', '=', 'wed_gradelvl.grad_id') 
            ->leftJoin('def_subject as wed_subject', 'wed_sched.sched_wed', '=', 'wed_subject.subj_id') 
    
            ->leftJoin('def_launch as thurs_launch', 'thurs_sched.sched_lnid', '=', 'thurs_launch.ln_id') 
            ->leftJoin('def_section as thurs_section', 'thurs_launch.ln_section', '=', 'thurs_section.sec_id') 
            ->leftJoin('def_gradelvl as thurs_gradelvl', 'thurs_launch.ln_gradelvl', '=', 'thurs_gradelvl.grad_id') 
            ->leftJoin('def_subject as thurs_subject', 'thurs_sched.sched_thurs', '=', 'thurs_subject.subj_id') 
    
            ->leftJoin('def_launch as fri_launch', 'fri_sched.sched_lnid', '=', 'fri_launch.ln_id') 
            ->leftJoin('def_section as fri_section', 'fri_launch.ln_section', '=', 'fri_section.sec_id') 
            ->leftJoin('def_gradelvl as fri_gradelvl', 'fri_launch.ln_gradelvl', '=', 'fri_gradelvl.grad_id') 
            ->leftJoin('def_subject as fri_subject', 'fri_sched.sched_fri', '=', 'fri_subject.subj_id') 
    
            ->leftJoin('def_launch as sat_launch', 'sat_sched.sched_lnid', '=', 'sat_launch.ln_id') 
            ->leftJoin('def_section as sat_section', 'sat_launch.ln_section', '=', 'sat_section.sec_id') 
            ->leftJoin('def_gradelvl as sat_gradelvl', 'sat_launch.ln_gradelvl', '=', 'sat_gradelvl.grad_id') 
            ->leftJoin('def_subject as sat_subject', 'sat_sched.sched_sat', '=', 'sat_subject.subj_id') 
        
    
            ->select(  
                'sched_occ.occ_time',

                'mon_sched.sched_lnid as mon_sched_lnid',
                'mon_sched.sched_mon_code as mon_subj_code',
                'mon_subject.subj_name as mon_subj_name',
                'mon_subject.subj_id as mon_subj_id',
                'mon_section.sec_code as mon_sec_code',
                'mon_section.sec_name as mon_sec_name',
                'mon_gradelvl.grad_code as mon_gradelvl_code',
                'mon_gradelvl.grad_name as mon_gradelvl_name',
                 
                'tue_sched.sched_lnid as tue_sched_lnid',
                'tue_sched.sched_tue_code as tue_subj_code',
                'tue_subject.subj_name as tue_subj_name',
                'tue_subject.subj_id as tue_subj_id',
                'tue_section.sec_code as tue_sec_code',
                'tue_section.sec_name as tue_sec_name',
                'tue_gradelvl.grad_code as tue_gradelvl_code',
                'tue_gradelvl.grad_name as tue_gradelvl_name',
                
                'wed_sched.sched_lnid as wed_sched_lnid',
                'wed_sched.sched_wed_code as wed_subj_code',
                'wed_subject.subj_name as wed_subj_name',
                'wed_subject.subj_id as wed_subj_id',
                'wed_section.sec_code as wed_sec_code',
                'wed_section.sec_name as wed_sec_name',
                'wed_gradelvl.grad_code as wed_gradelvl_code',
                'wed_gradelvl.grad_name as wed_gradelvl_name',
    
                'thurs_sched.sched_lnid as thurs_sched_lnid',
                'thurs_sched.sched_thurs_code as thurs_subj_code',
                'thurs_subject.subj_name as thurs_subj_name',
                'thurs_subject.subj_id as thurs_subj_id',
                'thurs_section.sec_code as thurs_sec_code',
                'thurs_section.sec_name as thurs_sec_name',
                'thurs_gradelvl.grad_code as thurs_gradelvl_code',
                'thurs_gradelvl.grad_name as thurs_gradelvl_name',
    
                'fri_sched.sched_lnid as fri_sched_lnid',
                'fri_sched.sched_fri_code as fri_subj_code',
                'fri_subject.subj_name as fri_subj_name',
                'fri_subject.subj_id as fri_subj_id',
                'fri_section.sec_code as fri_sec_code',
                'fri_section.sec_name as fri_sec_name',
                'fri_gradelvl.grad_code as fri_gradelvl_code',
                'fri_gradelvl.grad_name as fri_gradelvl_name',
    
                'sat_sched.sched_lnid as sat_sched_lnid',
                'sat_sched.sched_sat_code as sat_subj_code',
                'sat_subject.subj_name as sat_subj_name',
                'sat_subject.subj_id as sat_subj_id',
                'sat_section.sec_code as sat_sec_code',
                'sat_section.sec_name as sat_sec_name',
                'sat_gradelvl.grad_code as sat_gradelvl_code',
                'sat_gradelvl.grad_name as sat_gradelvl_name',
            )
            ->orderBy('sched_occ.occ_id','ASC')
            ->where('sched_occ.occ_mon_bid', '=', $bid)
            ->where('sched_occ.occ_mon_classrid', '=', $classrid)
            ->where('sched_occ.occ_tue_bid', '=', $bid)
            ->where('sched_occ.occ_tue_classrid', '=', $classrid)
            ->where('sched_occ.occ_wed_bid', '=', $bid)
            ->where('sched_occ.occ_wed_classrid', '=', $classrid)
            ->where('sched_occ.occ_thurs_bid', '=', $bid)
            ->where('sched_occ.occ_thurs_classrid', '=', $classrid)
            ->where('sched_occ.occ_fri_bid', '=', $bid)
            ->where('sched_occ.occ_fri_classrid', '=', $classrid)
            ->where('sched_occ.occ_sat_bid', '=', $bid)
            ->where('sched_occ.occ_sat_classrid', '=', $classrid)
            ->where('sched_occ.occ_status', '=', 1)
            ->get();
            
            return $occupancy;

    }

    public function addEmployee(Request $request)
    {
        $format1 = substr($request->input('per_firstname'), 0, 2);
        $format2 = substr($request->input('per_middlename'), 0, 2);
        $format3 = substr($request->input('per_lastname'), 0, 2);

        $randomizer = uniqid();

        $load = 'l-' .strtoupper($format1) . strtoupper($format2) . strtoupper($format3) . '-' .  $randomizer;

        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $primary = DB::table('def_employee')->insert([
                'emp_firstname' => $request->input('emp_firstname'),
                'emp_middlename' => $request->input('emp_middlename'),
                'emp_lastname' => $request->input('emp_lastname'),
                'emp_suffixname' => $request->input('emp_suffixname'),
                'emp_birthday' => $request->input('emp_birthday'),
                'emp_gender' => $request->input('emp_gender'),
                'emp_civilstatus' => $request->input('emp_civilstatus'),
                'emp_contact' => $request->input('emp_contact'),
                'emp_email' => $request->input('emp_email'),
                'emp_lid' => $load,
                'emp_addedby' => $request->input('emp_addedby'),
                'emp_dateadded' =>$date,
            ]);

            return $data = [
                'load' => $load,
                'status' => 204,
            ];
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }
    public function getEmployee($limit, $offset, $search)
    {
        if($search==204){

            if($limit == 0 && $offset == 0){
                $employee = DB::table('def_employee')
                ->leftJoin('def_department', 'def_employee.emp_depid', '=', 'def_department.dep_id') 
                ->select(  
                    'def_employee.*',
                    'def_department.*',
                )->orderBy('def_employee.emp_depid','DESC')
                ->get();
            }else{
                $employee = DB::table('def_employee')
                ->leftJoin('def_department', 'def_employee.emp_depid', '=', 'def_department.dep_id') 
                ->select(  
                    'def_employee.*',
                    'def_department.*',
                )->orderBy('def_employee.emp_depid','DESC')
                ->limit($limit)
                ->offset($offset)
                ->get();
            }
                          
            $count = DB::table('def_employee')
            ->leftJoin('def_department', 'def_employee.emp_depid', '=', 'def_department.dep_id') 
            ->select(  
                'def_employee.*',
                'def_department.*',
            )->count();
            
        }
        else{
            if($limit == 0 && $offset == 0){
                $employee = DB::table('def_employee')
                        ->leftJoin('def_department', 'def_employee.emp_depid', '=', 'def_department.dep_id') 
                        ->select(  
                            'def_employee.*',
                            'def_department.*',
                        )->orderBy('def_employee.emp_depid','DESC')
                        ->where('emp_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('emp_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('emp_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_dateadded', 'like',  '%' . $search .'%');
                        })
                        ->get();
            }else{
                $employee = DB::table('def_employee')
                        ->leftJoin('def_department', 'def_employee.emp_depid', '=', 'def_department.dep_id') 
                        ->select(  
                            'def_employee.*',
                            'def_department.*',
                        )->orderBy('def_employee.emp_depid','DESC')
                        ->where('emp_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('emp_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('emp_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_dateadded', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->get();
            }
             
            $count =  DB::table('def_employee')
                        ->leftJoin('def_department', 'def_employee.emp_depid', '=', 'def_department.dep_id') 
                        ->select(  
                            'def_employee.*',
                            'def_department.*',
                        )->orderBy('def_employee.emp_depid','DESC')
                        ->where('emp_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('emp_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('emp_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_suffixname', 'like',  '%' . $search .'%')
                            ->orWhere('emp_dateadded', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $employee,
            'count' => $count,
        ];
    }

    public function updateEmployee(Request $req)
    {
        try{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $s1 = DB::table('def_employee')
                ->where('emp_id','=', $req['emp_id'])
                ->update([
                    "emp_firstname" => $req['emp_firstname'],
                    "emp_middlename" => $req['emp_middlename'],
                    "emp_lastname" => $req['emp_lastname'],
                    "emp_suffixname" => $req['emp_suffixname'],
                    "emp_birthday" => $req['emp_birthday'],
                    "emp_gender" => $req['emp_gender'],
                    "emp_civilstatus" => $req['emp_civilstatus'],
                    "emp_contact" => $req['emp_contact'],
                    "emp_addedby" =>$req['emp_addedby'],
                    "emp_dateadded" => $date,
                ]);
            
            return $data = [
                'status' => 204,
            ];
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
        
       
    }

    public function addEmployeeLoad(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());
        try{

            if($request->input('ld_id') == 0){
                $primary = DB::table('def_employee_load')->insert([
                    'ld_empid' => $request->input('ld_empid'),
                    'ld_subjid' => $request->input('ld_subjid'),
                    'ld_addedby' => $request->input('ld_addedby'),
                    'ld_dateadded' => $date
                ]);
                return 204;
            }else{  
                return 204;
            }
        }
        catch (Exception $ex) {
            return 500;
        }
    }

    public function getEmployeeLoad($id)
    {
        if($id == 0){
            $load = DB::table('def_employee_load')
                ->orderBy('def_employee_load.ld_id','ASC')
                ->get();
        }else{
            $load = DB::table('def_employee_load')
                ->orderBy('def_employee_load.ld_id','ASC')
                ->where('def_employee_load.ld_empid', '=' , $id)
                ->get();
        }
        return $load;
       
    }

    public function deleteEmployeeLoad(Request $request)
    {
        $s1 = DB::table('def_employee_load')
        ->where('ld_id','=', $request['ld_id'])
        ->delete();
        return 200; 
       
    }
   
    public function deleteEmployee(Request $request)
    {
        $s1 = DB::table('def_employee')
        ->where('emp_id','=', $request['emp_id'])
        ->delete();
        return 200; 
       
    }

    public function deleteEnrollment(Request $request)
    {
        // $s1 = DB::table('def_enrollment')
        // ->where('enr_id','=', $request['enr_id'])
        // ->delete();
        // return 200; 

        try{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $s1 = DB::table('def_enrollment')
                ->where('enr_id','=', $request['enr_id'])
                ->update([
                    "enr_status" => 0,
                    "enr_updatedby" =>$request['enr_updatedby'],
                    "enr_dateupdated" => $date,
                ]);
            return $data = [
                'status' => 204,
            ];
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
       
    }


    public function getStudentMaster($limit, $offset, $search)
    {
        if($search==204){
            return $search;
        }else{
            return 'test';
        }
    }

    public function getScheduledSubjects($id)
    {

        $scheduled = DB::table('def_launch_schedule')->orderBy('sched_id')
        ->where('sched_lnid','=', $id)
        ->where('sched_status','=', 1)
        ->get();
        return $scheduled; 
    }

    public function getScheduledFaculty()
    {
        $faculty = DB::table('def_employee as employee')
        ->leftJoin('def_launch_faculty as faculty', 'employee.emp_id','=','faculty.lf_empid') 
        // ->leftJoin('def_department as department', 'def_employee.emp_depid', '=', 'def_department.dep_id') 
        ->select(  
            'faculty.*',
            'employee.emp_id',
            'employee.emp_firstname',
            'employee.emp_middlename',
            'employee.emp_lastname',
            'employee.emp_suffixname')

        ->where('employee.emp_status','=', 1)
        ->get();
        return $faculty; 

        // $faculty = DB::table('def_launch_faculty as faculty')
        // ->leftJoin('def_employee as employee', 'faculty.lf_empid', '=', 'employee.emp_id') 
        // // ->leftJoin('def_department as department', 'def_employee.emp_depid', '=', 'def_department.dep_id') 
        // ->select(  
        //     'faculty.*',
        //     'employee.emp_firstname',
        //     'employee.emp_middlename',
        //     'employee.emp_lastname',
        //     'employee.emp_suffixname')

        // ->orderBy('faculty.lf_id')
        // ->where('faculty.lf_status','=', 1)
        // ->get();
        // return $faculty; 
    }

    public function addScheduledFaculty(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());

        if(empty($request['lf_id'])){
            try{
                $primary = DB::table('def_launch_faculty')->insert([
                    'lf_empid' => $request->input('lf_empid'),
                    'lf_lnid' => $request->input('lf_lnid'),
                    'lf_subjid' => $request->input('lf_subjid'),
                    'lf_addedby' => $request->input('lf_addedby'),
                    'lf_dateadded' => $date
                ]);
                return $data = [
                    'status' => 204,
                ];
            }
            catch (Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }else{
            try{
                $s1 = DB::table('def_launch_faculty')
                    ->where('lf_id','=', $request['lf_id'])
                    ->where('lf_lnid','=', $request['lf_lnid'])
                    ->update([
                        'lf_empid' => $request->input('lf_empid'),
                        'lf_lnid' => $request->input('lf_lnid'),
                        'lf_subjid' => $request->input('lf_subjid'),
                        'lf_updatedby' => $request->input('lf_updatedby'),
                        'lf_dateupdated' => $date
                    ]);
                return $data = [
                    'status' => 204,
                ];
            }
            catch (Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }
    }


    public function deleteScheduledFaculty(Request $request)
    {
        $s1 = DB::table('def_launch_faculty')
        ->where('lf_id','=', $request['lf_id'])
        ->where('lf_empid','=', $request['lf_empid'])
        ->delete();
        return 200; 

        // date_default_timezone_set('Asia/Manila');
        // $date = date('Y-m-d h:i:s', time());

        // $s1 = DB::table('def_launch_faculty')
        // ->where('lf_id','=', $request['lf_id'])
        // ->where('lf_empid','=', $request['lf_empid'])
        // ->where('lf_lnid','=', $request['lf_lnid'])
        // ->update([
        //     'lf_updatedby' => $request->input('lf_updatedby'),
        //     'lf_dateupdated' => $date,
        //     'lf_status' => 0
        // ]);
        // return 200; 
    }

    // public function getFacultyAvailability()
    // {

    //     $availability = DB::table('def_launch_occupancy_faculty')->orderBy('occ_id')
    //     ->get();
    //     return $availability; 
    // }

    public function getFacultyAvailability()
    {

        $availability = DB::table('def_launch_occupancy_faculty')->orderBy('occ_id')
        ->get();
        return $availability; 
    }

    
    public function getStudentIdentification($id)
    {

        $identification = DB::table('def_student_identification')
        ->where('ident_personid','=',$id)
        ->first();
        return $identification; 
    }

    public function addStudentIdentification(Request $request)
    {

        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());

            if($request->input('ident_idno')){
                $primary = DB::table('def_student_identification')
                ->where('ident_idno','=', $request->input('ident_idno'))
                ->update([
                    'ident_personid' => $request->input('ident_personid'),
                    'ident_identification' => $request->input('ident_identification'),
                    'ident_updatedby' => $request->input('ident_updatedby'),
                    'ident_dateupdated' => $date,
                ]);
            }else{
                $primary = DB::table('def_student_identification')->insert([
                    'ident_personid' => $request->input('ident_personid'),
                    'ident_identification' => $request->input('ident_identification'),
                    'ident_addedby' => $request->input('ident_addedby'),
                    'ident_dateadded' => $date,
                ]);

            }
            return $data = [
                'status' => 204,
            ];
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }
    
    
}