<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class TransactionsController extends Controller
{
    public function getAccountsDetails()
    {
        $settlement = DB::table('def_accounts_settlement')
                          ->where('acs_status', '=',  1)
                          ->get();
        return $settlement;
    }

    public function getPriceDetails()
    {
        $price = DB::table('def_accounts_price')
                          ->where('acp_status', '=',  1)
                          ->get();
        return $price;
    }

    
    public function getFeeDetails($limit, $offset, $search)
    {
        if($search==204){

            if($limit == 0 && $offset == 0){
                $fee = DB::table('def_accounts_fee')
                ->select(  
                    'def_accounts_fee.*',
                )->orderBy('def_accounts_fee.acf_id','DESC')
                ->where('def_accounts_fee.acf_status', '=' , 1)
                ->get();
            }else{
                $fee = DB::table('def_accounts_fee')
                ->select(  
                    'def_accounts_fee.*',
                )->orderBy('def_accounts_fee.acf_id','DESC')
                ->where('def_accounts_fee.acf_status', '=' , 1)
                ->limit($limit)
                ->offset($offset)
                ->get();
            }
           
            $count = DB::table('def_accounts_fee')
            ->select(  
                'def_accounts_fee.*',
            )->where('def_accounts_fee.acf_status', '=' , 1)
            ->count();
        }
        else{
             $fee = DB::table('def_accounts_fee')
                        ->select(  
                            'def_accounts_fee.*',
                        )->orderBy('def_accounts_fee.acf_id','DESC')
                        ->where('def_accounts_fee.acf_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('acf_id', 'like',  '%' . $search .'%')
                            ->orWhere('acf_desc', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_accounts_fee')
                        ->select(  
                            'def_accounts_fee.*',
                        )->orderBy('def_accounts_fee.acf_id','DESC')
                        ->where('def_accounts_fee.acf_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('acf_id', 'like',  '%' . $search .'%')
                            ->orWhere('acf_desc', 'like',  '%' . $search .'%');
                        })
                        // ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $fee,
            'count' => $count,
        ];
        
    }

    
    public function getTransactionDetails($limit, $offset, $fname, $mname, $lname, $mode, $id, $type)
    {   
        if($mode == 1){
                $request = DB::table('def_accounts_request as dr')
                ->leftJoin('def_accounts_fee as df', 'dr.acr_reqitem', '=', 'df.acf_id')
                ->leftJoin('def_person as pr', 'dr.acr_personid', '=', 'pr.per_id')

                ->leftJoin('sett_ph_country as currcountry', 'pr.per_curr_country', '=', 'currcountry.countryCode') 
                ->leftJoin('sett_ph_province as currprovince', 'pr.per_curr_province', '=', 'currprovince.provCode') 
                ->leftJoin('sett_ph_city as currcity', 'pr.per_curr_city', '=', 'currcity.citymunCode') 
                ->leftJoin('sett_ph_barangay as currbarangay', 'pr.per_curr_barangay', '=', 'currbarangay.brgyCode') 

                ->select(  
                    'dr.*',
                    'df.*',
                    'pr.per_firstname',
                    'pr.per_middlename',
                    'pr.per_lastname',
                    'pr.per_suffixname',
                    'pr.per_curr_home',
                    'currcountry.name as currcountryname',
                    'currprovince.provDesc as currprovincename',
                    'currcity.citymunDesc as currcityname',
                    'currbarangay.brgyDesc as currbarangayname',
                )
                ->limit($limit)->offset($offset)
                ->orderBy('dr.acr_id','DESC')
                ->get();

                $count = DB::table('def_accounts_request as dr')
                ->leftJoin('def_accounts_fee as df', 'dr.acr_reqitem', '=', 'df.acf_id')
                ->leftJoin('def_person as pr', 'dr.acr_personid', '=', 'pr.per_id')
                ->select(  
                    'dr.*',
                )
                // ->limit($limit)->offset($offset)
                ->count();

        }elseif ($mode == 2){

            if($type == 1){
                // means bill for tuition
                $request = DB::table('def_accounts_settlement')
                        ->where('acs_id', '=', $id)
                        ->get();
                $count = DB::table('def_accounts_settlement')
                        ->where('acs_id', '=', $id)
                        ->count();
            }else{
                // means request
                $request = DB::table('def_accounts_request')
                        ->where('acr_id', '=', $id)
                        ->get();
                $count = DB::table('def_accounts_request')
                        ->where('acr_id', '=', $id)
                        ->count();
            }
           
        }
        elseif ($mode == 3){ //qr search
            $request = DB::table('def_accounts_request')
                    ->leftJoin('def_student_identification', 'def_accounts_request.acr_personid', '=', 'def_student_identification.ident_personid') 
                    ->where('def_student_identification.ident_identification', '=',  $id)
                    ->get();
            $count = DB::table('def_accounts_request')
                    ->leftJoin('def_student_identification', 'def_accounts_request.acr_personid', '=', 'def_student_identification.ident_personid') 
                    ->where('def_student_identification.ident_identification', '=',  $id)
                    ->count();
        }elseif ($mode == 5){ //qr search request
            $request = DB::table('def_accounts_request')
                    ->where('acr_reqheader', '=',  $id)
                    ->get();
            $count = DB::table('def_accounts_request')
                    ->where('acr_reqheader', '=',  $id)
                    ->count();
        }
        else{
            $request = DB::table('def_accounts_request as dr')
                ->leftJoin('def_accounts_fee as df', 'dr.acr_reqitem', '=', 'df.acf_id')
                ->leftJoin('def_person as pr', 'dr.acr_personid', '=', 'pr.per_id')
                ->select(  
                    'dr.*',
                    'df.*',
                    'pr.per_firstname',
                    'pr.per_middlename',
                    'pr.per_lastname',
                    'pr.per_suffixname',
                )
                ->where(function($query) use ($fname,$mname,$lname) {
                    $query->orWhere('pr.per_firstname', 'like',  '%' . $fname .'%')
                    ->orWhere('pr.per_middlename', 'like',  '%' . $mname .'%')
                    ->orWhere('pr.per_lastname', 'like',  '%' . $lname .'%');
                })
                ->limit($limit)->offset($offset)
                ->orderBy('dr.acr_id','DESC')
                ->get();

                $count = DB::table('def_accounts_request as dr')
                ->leftJoin('def_accounts_fee as df', 'dr.acr_reqitem', '=', 'df.acf_id')
                ->leftJoin('def_person as pr', 'dr.acr_personid', '=', 'pr.per_id')
                ->select(  
                    'dr.*',
                )
                 ->where(function($query) use ($fname,$mname,$lname) {
                    $query->orWhere('pr.per_firstname', 'like',  '%' . $fname .'%')
                    ->orWhere('pr.per_middlename', 'like',  '%' . $mname .'%')
                    ->orWhere('pr.per_lastname', 'like',  '%' . $lname .'%');
                })
                // ->limit($limit)->offset($offset)
                ->count();
        }

        return $data = [
            'data' => $request,
            'count' => $count,
        ];
        
    }

    public function addItemRequest(Request $request)
    {
        $randomizer = uniqid();

        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());

            $header = $request->input('acr_reqitem').'-'.$request->input('acr_personid').'-'.$randomizer;

            $primary = DB::table('def_accounts_request')->insert([
                'acr_amount' => $request->input('acr_amount'),
                'acr_personid' => $request->input('acr_personid'),
                'acr_personname' => $request->input('acr_personname'),
                'acr_reqitem' => $request->input('acr_reqitem'),
                'acr_paystatus' => $request->input('acr_paystatus'),
                'acr_addedby' => $request->input('acr_addedby'),
                'acr_docstamp' => $request->input('acr_docstamp'),
                'acr_dateadded' =>$date,
                'acr_reqheader' =>$header,
            ]);
            return $data = [
                'status' => 204,
                'header' => $header
            ];
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }

    public function deleteItemRequest(Request $request)
    {
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $s1 = DB::table('def_accounts_request')
            ->where('acr_id','=', $request['acr_id'])
            ->update([
                "acr_status" => 0,
                "acr_updatedby" => $request['acr_updatedby'],
                "acr_dateupdated" => $date,
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

    public function addPayment(Request $request)
    {
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());

           if($request['acy_billtype'] == 1){
                $s1 = DB::table('def_accounts_settlement')
                ->where('acs_id','=', $request['acy_accid'])
                ->update([
                    "acs_mode" => $request['acy_mode'],
                    "acs_receipt" => $request['acy_receipt'],
                    // "acs_amount" => $request['acy_payment'],
                    "acs_datesettled" => $date,
                    "acs_cashier" => $request['acy_cashier'],
                    "acs_updatedby" => $request['acy_cashier'],
                ]);   
           }else{
                $s1 = DB::table('def_accounts_request')
                ->where('acr_id','=', $request['acy_accid'])
                ->update([
                    "acr_paystatus" => $request['acr_paystatus'],
                    "acr_dateupdated" => $date,
                ]);
           }

           if($s1 != 0){
                $s2 = DB::table('def_accounts_payment')
                ->insert([
                    'acy_accid' => $request->input('acy_accid'),
                    'acy_mode' => $request->input('acy_mode'),
                    'acy_receipt' => $request->input('acy_receipt'),
                    'acy_payment' => $request->input('acy_payment'),
                    'acy_amount' => $request->input('acy_amount'),
                    'acy_partial' => $request->input('acy_partial'),
                    'acy_cashier' => $request->input('acy_cashier'),
                    'acy_balance' => $request->input('acy_balance'),
                    'acy_billtype' => $request->input('acy_billtype'),
                    'acy_datepaid' => $date,
                    'acy_cheque_no' => $request->input('acy_cheque_no'),
                    'acy_cheque_bank' => $request->input('acy_cheque_bank'),
                    'acy_bank_no' => $request->input('acy_bank_no'),
                    'acy_transferred_bank' => $request->input('acy_transferred_bank'),

                    'acy_series' => $request->input('acy_series'),
                    'acy_series_prefix' => $request->input('acy_series_prefix'),
                    'acy_series_year' => $request->input('acy_series_year'),
                    'acy_series_pattern' => $request->input('acy_series_pattern'),

                ]);
                return $data = [
                    'status' => 204,
                ];
           }
        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    }

    public function getPaymentDetails($id, $billtype)
    {   
        $payment = DB::table('def_accounts_payment')->orderBy('acy_accid','DESC')
        ->where('acy_accid', '=',  $id)
        ->where('acy_billtype', '=',  $billtype)
        ->get();

        return $data = [
            'data' => $payment,
        ];
    }

    public function getAllPayments($billtype, $dfrom, $dto, $cashier, $access)
    {   
        
        // $datefrom = date('Y-m-d h:i:s', strtotime($dfrom));
        // $dateto = date('Y-m-d h:i:s', strtotime($dto));

        $datefrom = date('Y-m-d 00:00:00', strtotime($dfrom));
        $dateto = date('Y-m-d H:i:s', strtotime($dto . ' +1 hour'));

        if($access == 1){
            //accountant
            switch ($billtype) {
                case 1:
                    $payment = DB::table('def_accounts_payment as ac')->orderBy('ac.acy_accid','DESC')
                    ->leftJoin('def_accounts_settlement as at', function($join)
                                    {
                                        $join->on('ac.acy_accid', '=', 'at.acs_id');
                                        $join->on('ac.acy_billtype', '=', 'at.acs_billtype');
                                    })
                    ->leftJoin('def_employee as emp', 'ac.acy_cashier', '=', 'emp.emp_accid')
                    ->leftJoin('def_person as prn', 'at.acs_personid', '=', 'prn.per_id')

                    ->select(  
                        'ac.*',
                        'at.*',
                        'emp.*',
                        'prn.*',
                    )
                    ->where('ac.acy_status', '=',  1)
                    ->where('ac.acy_billtype', '=',  $billtype)
                    ->whereBetween('ac.acy_datepaid', [$datefrom, $dateto])
                    ->orderBy('ac.acy_id','DESC')
                    ->get();
                    break;

                case 2:
                    $payment = DB::table('def_accounts_payment as ac')->orderBy('ac.acy_accid','DESC')
                    ->leftJoin('def_accounts_request as ar', function($join)
                                    {
                                        $join->on('ac.acy_accid', '=', 'ar.acr_id');
                                        $join->on('ac.acy_billtype', '=', 'ar.acr_billtype');
                                    })
                    ->leftJoin('def_employee as emp', 'ac.acy_cashier', '=', 'emp.emp_accid')
                    ->leftJoin('def_person as prn', 'ar.acr_personid', '=', 'prn.per_id')
                    ->leftJoin('def_accounts_fee as acf', 'ar.acr_reqitem', '=', 'acf.acf_id')

                    ->select(  
                        'ac.*',
                        'ar.*',
                        'emp.*',
                        'prn.*',
                        'acf.*',
                    )
                    ->where('ac.acy_status', '=',  1)
                    ->where('ac.acy_billtype', '=',  $billtype)
                    ->whereBetween('ac.acy_datepaid', [$datefrom, $dateto])
                    ->orderBy('ac.acy_id','DESC')
                    ->get();
                    break;


                default:

                    $payment = DB::table('def_accounts_payment as ac')
                        ->orderBy('ac.acy_accid','DESC')
                        
                        ->leftJoin('def_accounts_request as ar', function($join) {
                            $join->on('ac.acy_accid', '=', 'ar.acr_id');
                            $join->on('ac.acy_billtype', '=', 'ar.acr_billtype');
                        })
                        ->leftJoin('def_accounts_settlement as at', function($join) {
                            $join->on('ac.acy_accid', '=', 'at.acs_id');
                            $join->on('ac.acy_billtype', '=', 'at.acs_billtype');
                        })

                        // payments
                        ->leftJoin('def_employee as emp', 'ac.acy_cashier', '=', 'emp.emp_accid')
                        ->leftJoin('def_accounts_fee as acf', 'ar.acr_reqitem', '=', 'acf.acf_id')  

                        // academic details
                        ->leftJoin('def_person as prnsettle', 'at.acs_personid', '=', 'prnsettle.per_id')
                        ->leftJoin('def_person as prnrequest', 'ar.acr_personid', '=', 'prnrequest.per_id')
                        ->leftJoin('def_enrollment as enr', 'at.acs_enrid', '=', 'enr.enr_id')   
                        ->leftJoin('def_program as course', 'enr.enr_course', '=', 'course.prog_id')
                        ->leftJoin('sett_degree_types as program', 'enr.enr_program', '=', 'program.dtype_id')
                        ->leftJoin('def_gradelvl as gradelvl', 'enr.enr_gradelvl', '=', 'gradelvl.grad_id')
                        ->leftJoin('def_section as section', 'enr.enr_section', '=', 'section.sec_id')        

                        ->select(   
                            'ac.*',
                            'ar.*',
                            'at.*',
                            'emp.*',
                            'prnsettle.*',
                            'acf .*',
                            // Course / Arc course
                            DB::raw("(COALESCE(course.prog_code, 
                                    (SELECT arc.arc_course 
                                    FROM server_archive_persons as arc
                                    WHERE arc.arc_personid = prnrequest.per_id
                                    ORDER BY arc.arc_id ASC
                                    LIMIT 1), 'N/A')) as prog_code"),
                            // Grade level: use joined gradelvl or first fallback from subquery
                            DB::raw("(COALESCE(gradelvl.grad_name, 
                                    (SELECT sc.grad_name 
                                    FROM server_archive_persons as arc
                                    LEFT JOIN def_gradelvl sc ON arc.arc_gradelvl = sc.grad_id
                                    WHERE arc.arc_personid = prnrequest.per_id
                                    ORDER BY arc.arc_id ASC
                                    LIMIT 1), 'N/A')) as gradelvl_desc"),
                            // Section: use joined section or first fallback from subquery
                            DB::raw("(COALESCE(section.sec_name, 
                                    (SELECT sc.sec_name 
                                    FROM server_archive_persons as arc
                                    LEFT JOIN def_section sc ON arc.arc_section = sc.sec_id
                                    WHERE arc.arc_personid = prnrequest.per_id
                                    ORDER BY arc.arc_id ASC
                                    LIMIT 1), 'N/A')) as sec_desc"),
                            // program type
                            DB::raw("(COALESCE(program.dtype_desc, 
                                    (SELECT dt.dtype_desc 
                                    FROM server_archive_persons as arc
                                    LEFT JOIN sett_degree_types dt ON arc.arc_program = dt.dtype_id
                                    WHERE arc.arc_personid = prnrequest.per_id
                                    ORDER BY arc.arc_id ASC
                                    LIMIT 1), 'N/A')) as prog_desc")
                        )
                        ->distinct()
                        ->where('ac.acy_status', 1)
                        ->whereBetween('ac.acy_datepaid', [$datefrom, $dateto])
                        ->orderBy('ac.acy_id','DESC')
                        ->get();


                    break;
            }
        }else{
            //cashier
            switch ($billtype) {
                case 1:
                    $payment = DB::table('def_accounts_payment as ac')->orderBy('ac.acy_accid','DESC')
                    ->leftJoin('def_accounts_settlement as at', function($join)
                                    {
                                        $join->on('ac.acy_accid', '=', 'at.acs_id');
                                        $join->on('ac.acy_billtype', '=', 'at.acs_billtype');
                                    })
                    ->leftJoin('def_employee as emp', 'ac.acy_cashier', '=', 'emp.emp_accid')
                    ->leftJoin('def_person as prn', 'at.acs_personid', '=', 'prn.per_id')

                    ->select(  
                        'ac.*',
                        'at.*',
                        'emp.*',
                        'prn.*',
                    )
                    ->where('ac.acy_status', '=',  1)
                    ->where('ac.acy_billtype', '=',  $billtype)
                    ->where('ac.acy_cashier', '=',  $cashier)
                    ->whereBetween('ac.acy_datepaid', [$datefrom, $dateto])
                    ->orderBy('ac.acy_id','DESC')
                    ->get();
                    break;

                case 2:
                    $payment = DB::table('def_accounts_payment as ac')->orderBy('ac.emp_accid','DESC')
                    ->leftJoin('def_accounts_request as ar', function($join)
                                    {
                                        $join->on('ac.acy_accid', '=', 'ar.acr_id');
                                        $join->on('ac.acy_billtype', '=', 'ar.acr_billtype');
                                    })
                    ->leftJoin('def_employee as emp', 'ac.acy_cashier', '=', 'emp.emp_id')
                    ->leftJoin('def_person as prn', 'ar.acr_personid', '=', 'prn.per_id')
                    ->leftJoin('def_accounts_fee as acf', 'ar.acr_reqitem', '=', 'acf.acf_id')

                    ->select(  
                        'ac.*',
                        'ar.*',
                        'emp.*',
                        'prn.*',
                        'acf.*',
                    )
                    ->where('ac.acy_status', '=',  1)
                    ->where('ac.acy_billtype', '=',  $billtype)
                    ->where('ac.acy_cashier', '=',  $cashier)
                    ->whereBetween('ac.acy_datepaid', [$datefrom, $dateto])
                    ->orderBy('ac.acy_id','DESC')
                    ->get();
                    break;


                default:
                        $payment = DB::table('def_accounts_payment as ac')->orderBy('ac.acy_accid','DESC')
                        ->leftJoin('def_accounts_request as ar', function($join)
                                        {
                                            $join->on('ac.acy_accid', '=', 'ar.acr_id');
                                            $join->on('ac.acy_billtype', '=', 'ar.acr_billtype');
                                        })
                        ->leftJoin('def_accounts_settlement as at', function($join)
                                        {
                                            $join->on('ac.acy_accid', '=', 'at.acs_id');
                                            $join->on('ac.acy_billtype', '=', 'at.acs_billtype');
                                        })
                        ->leftJoin('def_employee as emp', 'ac.acy_cashier', '=', 'emp.emp_accid')
                        ->leftJoin('def_person as prn', 'at.acs_personid', '=', 'prn.per_id')
                        ->leftJoin('def_accounts_fee as acf', 'ar.acr_reqitem', '=', 'acf.acf_id')                
                        ->select(   
                            'ac.*',
                            'ar.*',
                            'at.*',
                            'emp.*',
                            'prn.*',
                            'acf.*',
                        )
                        ->where('ac.acy_status', '=',  1)
                        ->where('ac.acy_cashier', '=',  $cashier)
                        ->whereBetween('ac.acy_datepaid', [$datefrom, $dateto])
                        ->orderBy('ac.acy_id','DESC')
                        ->get();
                    break;
            }
        }


  


        return $data = [
            'data' => $payment,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
        ];
    }

    public function addAccountingItem(Request $request)
    {
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());

            if(empty($request->input('acf_id'))){

                $primary = DB::table('def_accounts_fee')->insert([
                    'acf_desc' => $request->input('acf_desc'),
                    'acf_docstamp' => $request->input('acf_docstamp'),
                    'acf_feetype' => $request->input('acf_feetype'),
                    'acf_rectype' => $request->input('acf_rectype'),
                    'acf_price' => $request->input('acf_price'),
                    'acf_docstamp' => $request->input('acf_docstamp'),
                    'acf_addedby' => $request->input('acf_user'),
                    'acf_dateadded' =>$date,
                ]);
                
                
            }else{
                if($request->input('acf_delete') == true){
                    $primary = DB::table('def_accounts_fee')
                    ->where('acf_id','=', $request['acf_id'])
                    ->update([
                        'acf_status' => 0,
                        'acf_updatedby' => $request->input('acf_user'),
                        'acf_dateupdated' =>$date,
                    ]);
                }else{
                    $primary = DB::table('def_accounts_fee')
                    ->where('acf_id','=', $request['acf_id'])
                    ->update([
                        'acf_desc' => $request->input('acf_desc'),
                        'acf_docstamp' => $request->input('acf_docstamp'),
                        'acf_feetype' => $request->input('acf_feetype'),
                        'acf_rectype' => $request->input('acf_rectype'),
                        'acf_price' => $request->input('acf_price'),
                        'acf_docstamp' => $request->input('acf_docstamp'),
                        'acf_updatedby' => $request->input('acf_user'),
                        'acf_dateupdated' =>$date,
                    ]);
                }
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

