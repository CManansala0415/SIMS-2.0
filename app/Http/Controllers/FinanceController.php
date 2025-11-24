<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class FinanceController extends Controller
{
    public function getSetSeries($id){

        if($id == 0){
  
            $query1 = DB::table('def_accounts_series as dcs')
                     ->leftJoin('def_employee as emp', 'dcs.sr_cashierid','=','emp.emp_accid') 
                     ->select(
                            'dcs.*',
                            'emp.emp_id',
                            \DB::raw("CONCAT_WS(' ', emp.emp_firstname, emp.emp_middlename, emp.emp_lastname, emp.emp_suffixname) AS fullname")
                        )
                     ->where('emp.emp_status','=', 1)
                     ->where('sr_receipt', '=',  1)
                     ->where('dcs.sr_status','=', 1)
                     ->get();
            $query2 = DB::table('def_accounts_series as dcs')
                    ->leftJoin('def_employee as emp', 'dcs.sr_cashierid','=','emp.emp_accid') 
                    ->select(
                        'dcs.*',
                        'emp.emp_id',
                        \DB::raw("CONCAT_WS(' ', emp.emp_firstname, emp.emp_middlename, emp.emp_lastname, emp.emp_suffixname) AS fullname")
                    )
                    ->where('emp.emp_status','=', 1)
                    ->where('sr_receipt', '=',  2)
                    ->where('dcs.sr_status','=', 1)
                    ->get();

            return [
                'or_series' => $query1,
                'pr_series' => $query2
            ];

        }else{
            $query1 = DB::table('def_accounts_series')
                    ->where('sr_cashierid', '=',  $id)
                    ->where('sr_receipt', '=',  1)
                    ->where('sr_status', '=',  1)
                    ->get();
            $query2 = DB::table('def_accounts_series')
                    ->where('sr_cashierid', '=',  $id)
                    ->where('sr_receipt', '=',  2)
                    ->where('sr_status', '=',  1)
                    ->get();
            return [
                'or_series' => $query1,
                'pr_series' => $query2
            ];
        }
        
        

    }

    public function saveSetSeries(Request $request,  $mode){

        try{

            //date time saving last to fix naten
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d h:i:s', time());

            if($request->input('sr_mode') == 1){
                if($request->input('sr_receipt') == 1){
                    $query = DB::table('def_accounts_series')->insert([
                        'sr_cashierid' => $request->input('cashierId'),
                        'sr_receipt' => $request->input('sr_receipt'),
                        'sr_prefix' => $request->input('sr_or_prefix'),
                        'sr_year' => $request->input('sr_or_year'),
                        'sr_start' => $request->input('sr_or_start'),
                        'sr_end' => $request->input('sr_or_end'),
                        'sr_addedby' =>$request->input('sr_user'),
                        'sr_dateadded' =>$date,
                    ]);
                }else{
                    $query = DB::table('def_accounts_series')->insert([
                        'sr_cashierid' => $request->input('cashierId'),
                        'sr_receipt' => $request->input('sr_receipt'),
                        'sr_prefix' => $request->input('sr_pr_prefix'),
                        'sr_year' => $request->input('sr_pr_year'),
                        'sr_start' => $request->input('sr_pr_start'),
                        'sr_end' => $request->input('sr_pr_end'),
                        'sr_addedby' =>$request->input('sr_user'),
                        'sr_dateadded' =>$date,
                    ]);
                }
                $query? $status = 200 : $status = 500;

            }else if ($request->input('sr_mode') == 2){
                if($request->input('sr_receipt') == 1){
                    $query = DB::table('def_accounts_series')
                    ->where('sr_id', '=',  $request->input('sr_id'))
                    ->update([
                        'sr_cashierid' => $request->input('cashierId'),
                        'sr_prefix' => $request->input('sr_or_prefix'),
                        'sr_year' => $request->input('sr_or_year'),
                        'sr_start' => $request->input('sr_or_start'),
                        'sr_end' => $request->input('sr_or_end'),
                        'sr_updatedby' =>$request->input('sr_user'),
                        'sr_dateupdated' =>$date,
                    ]);
                }else{
                    $query = DB::table('def_accounts_series')
                    ->where('sr_id', '=',  $request->input('sr_id'))
                    ->update([
                        'sr_cashierid' => $request->input('cashierId'),
                        'sr_prefix' => $request->input('sr_pr_prefix'),
                        'sr_year' => $request->input('sr_pr_year'),
                        'sr_start' => $request->input('sr_pr_start'),
                        'sr_end' => $request->input('sr_pr_end'),
                        'sr_updatedby' =>$request->input('sr_user'),
                        'sr_dateupdated' =>$date,
                    ]);

                }
                $query? $status = 200 : $status = 500;

            }else{
                 $query = DB::table('def_accounts_series')
                        ->where('sr_id','=', $request->input('sr_id'))
                        ->delete();
                $query? $status = 200 : $status = 500;
            }

            return $data = [
                'data' => $request->all(),
                'sr_mode' => $request->input('sr_mode'),
                'status' => $status
            ];
        }
        catch (Exception $ex) {
            return $data = [
                'data' => 'No Data',
                'status' => 500,
            ];
        }

    }

    public function getUsedSeries($seriesstart, $seriesend, $year, $prefix){
        try{
             $query = DB::table('def_accounts_payment')
            ->whereBetween('acy_series_pattern', [$seriesstart, $seriesend])
            ->where('acy_series_year','=',$year)
            ->where('acy_status','=',1)
            ->get();

            $query? $status = 200 : $status = 500;

             return $data = [
                'data' => $query,
                'status' => $status
            ];
        }catch (Exception $ex) {
            return $data = [
                'data' => 'No Data',
                'status' => 500,
            ];
        }

    }

    public function getCashiersDetails($id){
        try{
            $query = DB::table('def_employee')
            ->where('emp_depid','=',$id)
            ->where('emp_status','=',1)
            ->get();

            $query? $status = 200 : $status = 500;

             return $data = [
                'data' => $query,
                'status' => $status
            ];
            
        }catch (Exception $ex) {
            return $data = [
                'data' => 'No Data',
                'status' => 500,
            ];
        }

    }

    public function getCollectionStatus(){
        try {
            // Fetch record
            $query = DB::table('def_command_center')
                ->where('sett_code', 'cs_12')
                ->first();

            $query? $status = 200 : $status = 500;

            return [
                'data' => $query,
                'status' => $status
            ];

        } catch (Exception $ex) {
            return [
                'data' => 'No Data',
                'status' => 500
            ];
        }     
    }
    public function turnOffCollection(Request $request){
        try {
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');

            // Update status (0 or 1)
            $query = DB::table('def_command_center')
                ->where('sett_code', 'cs_12')
                ->update([
                    'sett_status'    =>  $request->input('status'),
                    'sett_updatedby' =>  $request->input('user_id'),
                    'sett_dateupdated' => $date,
                ]);

            $query? $status = 200 : $status = 500;

            return [
                'data' => $request->input('status'),
                'status' => $status
            ];


        } catch (Exception $ex) {
            return [
                'data' => 'No Data',
                'status' => 500
            ];
        }
    }

    public function getAccountsFee($id, $desc){
        try {

            $query = DB::table('def_accounts_fee')
            ->select(  
                'def_accounts_fee.*',
            )->orderBy('def_accounts_fee.acf_id','DESC')
            ->where('def_accounts_fee.acf_status', '=' , 1)
            ->get();

            $query? $status = 200 : $status = 500;

            return [
                'data' => $query,
                'status' => $status
            ];


        } catch (Exception $ex) {
            return [
                'data' => 'No Data',
                'status' => 500
            ];
        }
    }

    
    
}
