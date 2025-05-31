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
                        ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $fee,
            'count' => $count,
        ];
        
    }

    
    public function getRequestDetails($limit, $offset, $fname, $mname, $lname, $mode, $id)
    {   
        if($mode == 1){
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
                ->limit($limit)->offset($offset)
                ->orderBy('dr.acr_id','DESC')
                ->get();

                $count = DB::table('def_accounts_request as dr')
                ->leftJoin('def_accounts_fee as df', 'dr.acr_reqitem', '=', 'df.acf_id')
                ->leftJoin('def_person as pr', 'dr.acr_personid', '=', 'pr.per_id')
                ->select(  
                    'dr.*',
                )
                ->limit($limit)->offset($offset)
                ->count();

        }elseif ($mode == 2){
            $request = DB::table('def_accounts_request')
                    ->where('acr_id', '=', $id)
                    ->get();
            $count = DB::table('def_accounts_request')
                    ->where('acr_id', '=', $id)
                    ->count();
        }else{
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
                ->limit($limit)->offset($offset)
                ->count();
        }

        return $data = [
            'data' => $request,
            'count' => $count,
        ];
        
    }

    public function addItemRequest(Request $request)
    {
        try{
            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());
            $primary = DB::table('def_accounts_request')->insert([
                'acr_amount' => $request->input('acr_amount'),
                'acr_personid' => $request->input('acr_personid'),
                'acr_personname' => $request->input('acr_personname'),
                'acr_reqitem' => $request->input('acr_reqitem'),
                'acr_paystatus' => $request->input('acr_paystatus'),
                'acr_addedby' => $request->input('acr_addedby'),
                'acr_docstamp' => $request->input('acr_docstamp'),
                'acr_dateadded' =>$date,
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
                    "acs_payment" => $request['acy_payment'],
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

    public function getAllPayments($billtype, $dfrom, $dto, $paystat)
    {   

        $datefrom = date('Y-m-d h:i:s', strtotime($dfrom));
        $dateto = date('Y-m-d h:i:s', strtotime($dto));

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
        ->leftJoin('def_employee as emp', 'ac.acy_cashier', '=', 'emp.emp_id')
        ->leftJoin('def_person as prn', 'at.acs_personid', '=', 'prn.per_id')

        ->select(  
            'ac.*',
            'ar.*',
            'at.*',
            'emp.*',
            'prn.*',
        )
        ->where('ac.acy_status', '=',  1)
        ->where('ac.acy_billtype', '=',  $billtype)
        ->whereBetween('ac.acy_datepaid', [$datefrom, $dateto])
        ->orderBy('ac.acy_id','DESC')
        ->get();

        

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

