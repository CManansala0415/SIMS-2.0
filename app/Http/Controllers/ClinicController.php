<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class ClinicController extends Controller
{
    public function addStudentClinicalRecord(Request $request){
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');
            $primary = DB::table('def_clinic_checkup_students')->insert([
                'clnc_complaint' => $request->input('clnc_complaint'),
                'clnc_personid' => $request->input('clnc_personid'),
                'clnc_intervention' => $request->input('clnc_intervention'),
                'clnc_evaluation' => $request->input('clnc_evaluation'),
                'clnc_nurse' => $request->input('clnc_nurse'),
                'clnc_date' =>$date,
                'clnc_1st_dose' => $request->input('clnc_1st_dose'),
                'clnc_2nd_dose' => $request->input('clnc_2nd_dose'),
                'clnc_3rd_dose' => $request->input('clnc_3rd_dose'),
                'clnc_fam_illness' => $request->input('clnc_fam_illness'),
                'clnc_social_history' => $request->input('clnc_social_history'),
                'clnc_height' => $request->input('clnc_height'),
                'clnc_weight' => $request->input('clnc_weight'),
                'clnc_allergy' => $request->input('clnc_allergy'),
                'clnc_item' => $request->input('clnc_item'),
                'clnc_diet' => $request->input('clnc_diet'),
            ]);

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
    }

    public function getStudentClinicalRecord($mode, $id)
    {
        if($mode == 1){

            $clinicaldata = DB::table('def_clinic_checkup_students as cls')
                ->leftJoin('def_employee as emp', 'cls.clnc_nurse', '=', 'emp.emp_id')
                ->select(  
                    'cls.*',
                    'emp.*',
                )
                ->where('cls.clnc_status', '=',  1)
                ->where('cls.clnc_personid', '=',  $id)
                ->orderBy('cls.clnc_id','DESC')
                ->get();
          
        }else{
            $clinicaldata = DB::table('def_clinic_checkup_students as cls')
                ->leftJoin('def_employee as emp', 'cls.clnc_nurse', '=', 'emp.emp_id')
                ->select(  
                    'cls.*',
                    'emp.*',
                )
                ->where('cls.clnc_status', '=',  1)
                ->orderBy('cls.clnc_id','DESC')
                ->get();
        }
        
        return $clinicaldata;
    }

    public function addEmployeeClinicalRecord(Request $request){
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');
            $primary = DB::table('def_clinic_checkup_employees')->insert([
                'clne_complaint' => $request->input('clne_complaint'),
                'clne_personid' => $request->input('clne_personid'),
                'clne_intervention' => $request->input('clne_intervention'),
                'clne_evaluation' => $request->input('clne_evaluation'),
                'clne_nurse' => $request->input('clne_nurse'),
                'clne_date' =>$date,
                'clne_1st_dose' => $request->input('clne_1st_dose'),
                'clne_2nd_dose' => $request->input('clne_2nd_dose'),
                'clne_3rd_dose' => $request->input('clne_3rd_dose'),
                'clne_fam_illness' => $request->input('clne_fam_illness'),
                'clne_social_history' => $request->input('clne_social_history'),
                'clne_height' => $request->input('clne_height'),
                'clne_weight' => $request->input('clne_weight'),
                'clne_allergy' => $request->input('clne_allergy'),
                'clne_item' => $request->input('clne_item'),
                'clne_diet' => $request->input('clne_diet'),
            ]);

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
    }

    public function getEmployeeClinicalRecords($mode, $id)
    {
        if($mode == 1){

            $clinicaldata = DB::table('def_clinic_checkup_employees as cle')
                ->leftJoin('def_employee as emp', 'cle.clne_nurse', '=', 'emp.emp_id')
                ->select(  
                    'cle.*',
                    'emp.*',
                )
                ->where('cle.clne_status', '=',  1)
                ->where('cle.clne_personid', '=',  $id)
                ->orderBy('cle.clne_id','DESC')
                ->get();
          
        }else{
            $clinicaldata = DB::table('def_clinic_checkup_employees as cle')
                ->leftJoin('def_employee as emp', 'cle.clne_nurse', '=', 'emp.emp_id')
                ->select(  
                    'cle.*',
                    'emp.*',
                )
                ->where('cle.clne_status', '=',  1)
                ->orderBy('cle.clne_id','DESC')
                ->get();
        }
        
        return $clinicaldata;
    }

    
    public function getMedicalSupplies($limit, $offset, $search)
    {
        if($search==204){

            if($limit == 0 && $offset == 0){
                $medsupplies = DB::table('def_clinic_medical_supplies as cms')
                ->select(  
                    'cms.*',
                )->orderBy('cms.clms_id','DESC')
                ->where('cms.clms_status', '=' , 1)
                ->get();
            }else{
                $medsupplies = DB::table('def_clinic_medical_supplies as cms')
                ->select(  
                    'cms.*',
                )->orderBy('cms.clms_id','DESC')
                ->where('cms.clms_status', '=' , 1)
                ->limit($limit)
                ->offset($offset)
                ->get();
            }
           
            $count = DB::table('def_clinic_medical_supplies as cms')
            ->select(  
                'cms.*',
            )->where('cms.clms_status', '=' , 1)
            ->count();
        }
        else{
             $medsupplies = DB::table('def_clinic_medical_supplies as cms')
                        ->select(  
                            'cms.*',
                        )->orderBy('cms.clms_id','DESC')
                        ->where('cms.clms_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('clms_desc', 'like',  '%' . $search .'%')
                            ->orWhere('clms_id', 'like',  '%' . $search .'%')
                            ->orWhere('clms_stocks', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_clinic_medical_supplies as cms')
                        ->select(  
                            'cms.*',
                        )->orderBy('cms.clms_id','DESC')
                        ->where('cms.clms_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('clms_desc', 'like',  '%' . $search .'%')
                            ->orWhere('clms_id', 'like',  '%' . $search .'%')
                            ->orWhere('clms_stocks', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $medsupplies,
            'count' => $count,
        ];
    }

    public function updateMedicalSupply(Request $request){
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');
            $month = date('m', time());
            $year = date('Y', time());

            if($request->input('clms_mode') == 1){
                $primary = DB::table('def_clinic_medical_supplies')
                ->where('clms_id','=', $request->input('clms_id'))
                ->update([
                    'clms_desc' => $request->input('clms_desc'),
                    'clms_stocks' => $request->input('clms_stocks'),
                    'clms_itemtype' => $request->input('clms_itemtype'),
                    'clms_updatedby' => $request->input('clms_user'),
                    'clms_dateupdated' => $date
                ]);
            }elseif($request->input('clms_mode') == 2){
                $primary = DB::table('def_clinic_medical_supplies')->insert([
                    'clms_desc' => $request->input('clms_desc'),
                    'clms_stocks' => $request->input('clms_stocks'),
                    'clms_itemtype' => $request->input('clms_itemtype'),
                    'clms_addedby' => $request->input('clms_user'),
                    'clms_dateadded' => $date
                ]);
            }
            elseif($request->input('clms_mode') == 3){

                $stock = $request->input('clms_stocks');
                $replenish = $request->input('clsh_replenish');
                $balance = $stock + $replenish;

                $primary = DB::table('def_clinic_medical_supplies')
                ->where('clms_id','=', $request->input('clms_id'))
                ->update([
                    'clms_stocks' => $balance,
                    'clms_updatedby' => $request->input('clms_user'),
                    'clms_dateupdated' => $date
                ]);

                $secondary = DB::table('def_clinic_medical_supplies_tracker')->insert([
                    'clsh_clmsid' => $request->input('clms_id'),
                    'clsh_stocks' => $request->input('clms_stocks'),
                    'clsh_replenish' => $replenish,
                    'clsh_deduct' => $request->input('clsh_deduct'),
                    'clsh_balance' => $balance,
                    'clsh_month' => $month,
                    'clsh_year' => $year,
                    'clsh_addedby' => $request->input('clms_user'),
                    'clsh_dateadded' => $date
                ]);

            }else{

                $primary = DB::table('def_clinic_medical_supplies')
                ->where('clms_id','=', $request->input('clms_id'))
                ->update([
                    'clms_status' => 0,
                    'clms_updatedby' => $request->input('clms_user'),
                    'clms_dateupdated' => $date
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
    }

    public function dispenseMedicalSupplyStudent(Request $request){
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');

            $stock = $request->input('clmd_stock');
            $quantity = $request->input('clmd_quantity');
            $deduct = $stock - $quantity;
            $month = date('m', time());
            $year = date('Y', time());

            $primary = DB::table('def_clinic_medical_supplies')
            ->where('clms_id','=', $request->input('clmd_itemid'))
            ->update([
                'clms_stocks' => $deduct,
            ]);

            $secondary = DB::table('def_clinic_dispense_student')->insert([
                'clmd_personid' => $request->input('clmd_personid'),
                'clmd_deducted_stock' => $deduct,
                'clmd_stock' => $request->input('clmd_stock'),
                'clmd_quantity' => $request->input('clmd_quantity'),
                'clmd_itemid' => $request->input('clmd_itemid'),
                'clmd_dispensedby' => $request->input('clmd_user'),
                'clmd_datedispensed' => $date
            ]);

            $tertiary = DB::table('def_clinic_medical_supplies_tracker')->insert([
                'clsh_clmsid' => $request->input('clmd_itemid'),
                'clsh_stocks' => $request->input('clmd_stock'),
                'clsh_replenish' => 0,
                'clsh_deduct' => $quantity,
                'clsh_balance' => $deduct,
                'clsh_month' => $month,
                'clsh_year' => $year,
                'clsh_addedby' => $request->input('clmd_user'),
                'clsh_dateadded' => $date
            ]);
            
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
    }

    public function getDispensedMedicalSupplyStudent($id)
    {

        $dispenseditem = DB::table('def_clinic_dispense_student as cmd')
                ->leftJoin('def_employee as emp', 'cmd.clmd_dispensedby', '=', 'emp.emp_id')
                ->leftJoin('def_clinic_medical_supplies as cms', 'cmd.clmd_itemid', '=', 'cms.clms_id')
                ->select(  
                    'cmd.*',
                    'emp.*',
                    'cms.*',
                )
                ->where('cmd.clmd_status', '=',  1)
                ->where('cmd.clmd_personid', '=',  $id)
                ->orderBy('cmd.clmd_id','DESC')
                ->get();
        
        return $dispenseditem;
        
    }

    public function dispenseMedicalSupplyEmployee(Request $request){
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');

            $stock = $request->input('clmd_stock');
            $quantity = $request->input('clmd_quantity');
            $deduct = $stock - $quantity;
            $month = date('m', time());
            $year = date('Y', time());

            $primary = DB::table('def_clinic_medical_supplies')
            ->where('clms_id','=', $request->input('clmd_itemid'))
            ->update([
                'clms_stocks' => $deduct,
            ]);

            $secondary = DB::table('def_clinic_dispense_employee')->insert([
                'clmd_personid' => $request->input('clmd_personid'),
                'clmd_deducted_stock' => $deduct,
                'clmd_stock' => $request->input('clmd_stock'),
                'clmd_quantity' => $request->input('clmd_quantity'),
                'clmd_itemid' => $request->input('clmd_itemid'),
                'clmd_dispensedby' => $request->input('clmd_user'),
                'clmd_datedispensed' => $date
            ]);
            
            $tertiary = DB::table('def_clinic_medical_supplies_tracker')->insert([
                'clsh_clmsid' => $request->input('clms_id'),
                'clsh_stocks' => $request->input('clms_stocks'),
                'clsh_replenish' => 0,
                'clsh_deduct' => $quantity,
                'clsh_balance' => $deduct,
                'clsh_month' => $month,
                'clsh_year' => $year,
                'clsh_addedby' => $request->input('clmd_user'),
                'clsh_dateadded' => $date
            ]);

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
    }

    public function getDispensedMedicalSupplyEmployee($id)
    {
        $dispenseditem = DB::table('def_clinic_dispense_employee as cmd')
                ->leftJoin('def_employee as emp', 'cmd.clmd_dispensedby', '=', 'emp.emp_id')
                ->leftJoin('def_clinic_medical_supplies as cms', 'cmd.clmd_itemid', '=', 'cms.clms_id')
                ->select(  
                    'cmd.*',
                    'emp.*',
                    'cms.*',
                )
                ->where('cmd.clmd_status', '=',  1)
                ->where('cmd.clmd_personid', '=',  $id)
                ->orderBy('cmd.clmd_id','DESC')
                ->get();
        
        return $dispenseditem;
        
    }

    public function addClinicIshihara(Request $request, $type, $userid, $personid){
        //date time saving last to fix naten
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');
        $data = $request->all();

        $randomizer = uniqid();
        $examid = 'exam'. '-' . $randomizer;

        try{
            //create header then items
            $primary = DB::table('def_clinic_examination_header')->insert([
                'clhd_header' => $examid,
                'clhd_examtype' => $type,
                'clhd_addedby' => $userid,
                'clhd_dateadded' => $date,
                'clhd_personid' => $personid,
            ]);

            if($primary){
                foreach ($data as $key => $value) {
                    try{
                        $secondary = DB::table('def_clinic_examination_ishihara')->insert([
                            'cler_headerid' => $examid,
                            'cler_assessment' => $value['cler_assessment'],
                            'cler_plateid' => $value['cler_id'],
                            'cler_personid' => $value['cler_personid'],
                            'cler_remarks' => $value['cler_remarks'],
                            'cler_addedby' => $value['cler_user'],
                            'cler_dateadded' => $date
                        ]);
                    
                    }
                    catch (Exception $ex) {
                        return $data = [
                            'status' => 500,
                        ];
                    }
                }
            }
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
       

        return $data = [
            'data' => $request,
            'status' => 200,
        ];
    }

    public function addClinicHearing(Request $request, $type, $userid, $personid){
        //date time saving last to fix naten
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');
        $data = $request->all();

        $randomizer = uniqid();
        $examid = 'exam'. '-' . $randomizer;
        // return $value['no_plate']

        try{
             //create header then items
             $primary = DB::table('def_clinic_examination_header')->insert([
                'clhd_header' => $examid,
                'clhd_examtype' => $type,
                'clhd_addedby' => $userid,
                'clhd_dateadded' => $date,
                'clhd_personid' => $personid,
            ]);

            if($primary){
                foreach ($data as $key => $value) {
                    try{
        
                        $secondary = DB::table('def_clinic_examination_hearing')->insert([
                            'cleh_headerid' => $examid,
                            'cleh_assessment' => $value['cleh_assessment'],
                            'cleh_plateid' => $value['cleh_id'],
                            'cleh_personid' => $value['cleh_personid'],
                            'cleh_remarks' => $value['cleh_remarks'],
                            'cleh_addedby' => $value['cleh_user'],
                            'cleh_dateadded' => $date
                        ]);
                    
                    }
                    catch (Exception $ex) {
                        return $data = [
                            'status' => 500,
                        ];
                    }
                }
            }
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }

        return $data = [
            'data' => $request,
            'status' => 200,
        ];
    }

    public function getMedicalIshihara($id, $headerid){
        $clinicaldata = DB::table('def_clinic_examination_ishihara as cli')
            ->leftJoin('users as usc', 'usc.id', '=', 'cli.cler_addedby')
            ->leftJoin('def_employee as emp', 'emp.emp_accid', '=', 'usc.id')
            ->select(  
                'cli.*',
                'emp.*',
                'usc.*'
            )
            ->where('cli.cler_status', '=',  1)
            ->where('cli.cler_personid', '=',  $id)
            ->where('cli.cler_headerid', '=',  $headerid)
            ->get();

         return $clinicaldata;
    }

    public function getMedicalHearing($id, $headerid){
        $clinicaldata = DB::table('def_clinic_examination_hearing as clh')
            ->leftJoin('users as usc', 'usc.id', '=', 'clh.cleh_addedby')
            ->leftJoin('def_employee as emp', 'emp.emp_accid', '=', 'usc.id')
            ->select(  
                'clh.*',
                'emp.*',
                'usc.*'
            )
            ->where('clh.cleh_status', '=',  1)
            ->where('clh.cleh_personid', '=',  $id)
            ->where('clh.cleh_headerid', '=',  $headerid)
            ->get();

        return $clinicaldata;
    }

    public function getMedicalHeader($id, $type){
        $clinicaldata = DB::table('def_clinic_examination_header as cli')
            ->leftJoin('users as usc', 'usc.id', '=', 'cli.clhd_addedby')
            ->leftJoin('def_employee as emp', 'emp.emp_accid', '=', 'usc.id')
            ->select(  
                'cli.*',
                'emp.*',
                'usc.*'
            )
            ->where('cli.clhd_status', '=',  1)
            ->where('cli.clhd_examtype', '=',  $type)
            ->where('cli.clhd_personid', '=',  $id)
            ->get();

         return $clinicaldata;
    }

    public function getMedicalFiles($id, $folder){
        $medicalfiles = DB::table('def_clinic_medical_files as clf')
            ->leftJoin('users as usc', 'usc.id', '=', 'clf.clmf_addedby')
            ->leftJoin('def_employee as emp', 'emp.emp_accid', '=', 'usc.id')
            ->select(  
                'clf.*',
                'emp.*',
                'usc.*'
            )
            ->where('clf.clmf_status', '=',  1)
            ->where('clf.clmf_resulttype', '=',    $folder)
            ->where('clf.clmf_headerid', '=',  $id)
            ->get();

         return $medicalfiles;
    }

    public function getMedicalFileHeaders($id){
        $medicalfiles = DB::table('def_clinic_medical_files_header as clh')
            ->leftJoin('users as usc', 'usc.id', '=', 'clh.clfh_addedby')
            ->leftJoin('def_employee as emp', 'emp.emp_accid', '=', 'usc.id')
            ->select(  
                'clh.*',
                'emp.*',
                'usc.*'
            )
            ->where('clh.clfh_status', '=',  1)
            ->where('clh.clfh_personid', '=',  $id)
            ->get();

         return $medicalfiles;
    }

    public function addMedicalFileHeader(Request $request){
        //date time saving last to fix naten
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');
        $data = $request->all();

        $randomizer = uniqid();
        $archive = 'archive'. '-' . $randomizer;
        // return $value['no_plate']

        try{
             //create header then items
             $primary = DB::table('def_clinic_medical_files_header')->insert([
                'clfh_headerid' => $archive,
                'clfh_personid' => $request->input('clfh_personid'),
                'clfh_desc' => $request->input('clfh_desc'),
                'clfh_addedby' =>$request->input('clfh_user'),
                'clfh_dateadded' => $date,
            ]);

        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }

        return $data = [
            'data' => $request,
            'status' => 200,
        ];
    }

    
}
