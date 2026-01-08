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

     public function getTuitionInformation($limit, $offset, $mode, $item){
        try {

            switch($mode){
                case 1:
                    break;
                case 2:
                    break;
                default:
                    $query = DB::table('def_accounts_tuition_header as act')
                    ->leftJoin('def_program as course', 'act.act_course', '=', 'course.prog_id')
                    ->leftJoin('sett_degree_types as program', 'act.act_program', '=', 'program.dtype_id')
                    ->leftJoin('def_gradelvl as gradelvl', 'act.act_gradelvl', '=', 'gradelvl.grad_id')
                    ->leftJoin('def_section as section', 'act.act_section', '=', 'section.sec_id')      
                    ->leftJoin('sett_quarter as quarter', 'act.act_sem', '=', 'quarter.quar_id')      
                    ->select(
                        'act.*',
                        'prog_name as prog_code',
                        'grad_name as gradelvl_desc',
                        'sec_name as sec_desc',
                        'dtype_desc as prog_desc',
                        'quar_desc as quar_desc'
                    )
                    // ->where('act_status', '=' , 1)
                    ->limit($limit)
                    ->offset($offset)
                    ->get();

                    $count = DB::table('def_accounts_tuition_header')
                    // ->where('act_status', '=' , 1)
                    ->count();
                    break;
            }

            $query? $status = 200 : $status = 500;

            return [
                'data' => $query,
                'count' => $count,
                'status' => $status
            ];


        } catch (Exception $ex) {
            return [
                'data' => 'No Data',
                'status' => 500
            ];
        }
    }

    public function editAccountingTuition(Request $request){
        try {
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');

            if ($request->input('act_mode') == 'edit') {

                $query = DB::table('def_accounts_tuition_header')
                    ->where('act_id', $request->input('act_id'))
                    ->update([
                        'act_description' => $request->input('act_description'),
                        'act_course'      => $request->input('act_course'),
                        'act_program'     => $request->input('act_program'),
                        'act_type'        => $request->input('act_type'),
                        'act_sem'         => $request->input('act_sem'),
                        'act_section'     => $request->input('act_section'),
                        'act_gradelvl'    => $request->input('act_gradelvl'),
                        'act_updatedby'   => $request->input('act_user'),
                        'act_curriculum'   => $request->input('act_curriculum'),
                        'act_dateupdated' => $date,
                        
                    ]);

            } elseif ($request->input('act_mode') == 'delete') {

                $query = DB::table('def_accounts_tuition_header')
                    ->where('act_id', $request->input('act_id'))
                    ->delete();

            } elseif ($request->input('act_mode') == 'charge') {
                $query = DB::table('def_accounts_tuition_header')
                    ->where('act_id', $request->input('act_id'))
                    ->update([
                        'act_status'    => $request->input('act_status'),                     // ✅ deactivate
                        'act_updatedby' => $request->input('act_user'),
                        'act_dateupdated' => $date,
                    ]);
            } 
            
            else {
                $randomizer = uniqid();
                $headerid = 'acct-' .  $randomizer;

                $query = DB::table('def_accounts_tuition_header')
                    ->insert([
                        'act_description' => $request->input('act_description'),
                        'act_headerid' => $headerid,
                        'act_course'      => $request->input('act_course'),
                        'act_program'     => $request->input('act_program'),
                        'act_type'        => $request->input('act_type'),
                        'act_sem'         => $request->input('act_sem'),
                        'act_section'     => $request->input('act_section'),
                        'act_gradelvl'    => $request->input('act_gradelvl'),
                        'act_addedby'     => $request->input('act_user'),
                        'act_curriculum'   => $request->input('act_curriculum'),
                        'act_dateadded'   => $date,
                    ]);
            }

           
            $query? $status = 200 : $status = 500;

            return [
                'data' => $request->all(),
                'status' => $status
            ];


        } catch (Exception $ex) {
            return [
                'data' => 'No Data',
                'status' => 500
            ];
        }
    }

    public function saveTuitionTemplate(Request $request, $mode){
        try {
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');

            if($mode == 1){ // means update or insert
                foreach ($request->all() as $item) {
                    $randomizer = uniqid();
                    $custid = 'custid-' .  $randomizer;

                    if ($item['item_mode'] == 'update') {
                        $query = DB::table('def_accounts_tuition_template')
                        ->where('tuitemp_id','=', $item['tuitemp_id'])
                        ->update([
                            'tuitemp_headerid'   => $item['tuitemp_headerid'] ?? null,
                            'tuitemp_itemid'     => $item['tuitemp_itemid'] ?? null,
                            'tuitemp_subjid'     => $item['tuitemp_subjid'] ?? null,
                            'tuitemp_custid'     => (
                                !empty($item['tuitemp_itemid']) || !empty($item['tuitemp_subjid'])
                            ) ? null : $custid,
                            'tuitemp_custype'     => $item['tuitemp_custype'] ?? null,
                            'tuitemp_desc'       => $item['tuitemp_desc'] ?? null,
                            'tuitemp_price'      => $item['tuitemp_price'] ?? null,
                            'tuitemp_lec_price'      => $item['tuitemp_lec_price'] ?? null,
                            'tuitemp_lab_price'      => $item['tuitemp_lab_price'] ?? null,
                            'tuitemp_lec'      => $item['tuitemp_lec'] ?? null,
                            'tuitemp_lab'      => $item['tuitemp_lab'] ?? null,
                            'tuitemp_sem'      => $item['tuitemp_sem'] ?? null,
                            'tuitemp_gradelvl'      => $item['tuitemp_gradelvl'] ?? null,
                            'tuitemp_updatedby'    => $item['tuitemp_user'] ?? null,
                            'tuitemp_dateupdated'  => $date,
                        ]);

                    }else {
                        $query = DB::table('def_accounts_tuition_template')
                        ->insert([
                            'tuitemp_headerid'   => $item['tuitemp_headerid'] ?? null,
                            'tuitemp_itemid'     => $item['tuitemp_itemid'] ?? null,
                            'tuitemp_subjid'     => $item['tuitemp_subjid'] ?? null,
                            'tuitemp_custid'     => (
                                !empty($item['tuitemp_itemid']) || !empty($item['tuitemp_subjid'])
                            ) ? null : $custid,
                            'tuitemp_custype'     => $item['tuitemp_custype'] ?? null,
                            'tuitemp_desc'       => $item['tuitemp_desc'] ?? null,
                            'tuitemp_price'      => $item['tuitemp_price'] ?? null,
                            'tuitemp_lec_price'      => $item['tuitemp_lec_price'] ?? null,
                            'tuitemp_lab_price'      => $item['tuitemp_lab_price'] ?? null,
                            'tuitemp_lec'      => $item['tuitemp_lec'] ?? null,
                            'tuitemp_lab'      => $item['tuitemp_lab'] ?? null,
                            'tuitemp_sem'      => $item['tuitemp_sem'] ?? null,
                            'tuitemp_gradelvl'      => $item['tuitemp_gradelvl'] ?? null,
                            'tuitemp_addedby'    => $item['tuitemp_user'] ?? null,
                            'tuitemp_dateadded'  => $date,
                        ]);
                    }

                    
                }
            }else{
                $query = DB::table('def_accounts_tuition_template')
                ->where('tuitemp_id','=', $request->input('tuitemp_id'))
                ->delete();
            }

            $query? $status = 200 : $status = 500;

            return [
                'data' => $request->all(),
                'status' => $status
            ];


        } catch (Exception $ex) {
            return [
                'data' => 'No Data',
                'status' => 500
            ];
        }
    }

    public function getTuitionTemplate($id){
        try {

            $query = DB::table('def_accounts_tuition_template as tt')
            ->leftJoin('def_accounts_tuition_header as th', 'tt.tuitemp_headerid', '=', 'th.act_id') 
            ->leftJoin('def_program as course', 'th.act_course', '=', 'course.prog_id')
            ->leftJoin('sett_degree_types as program', 'th.act_program', '=', 'program.dtype_id')
            ->leftJoin('def_gradelvl as gradelvl', 'tt.tuitemp_gradelvl', '=', 'gradelvl.grad_id')
            ->leftJoin('def_section as section', 'th.act_section', '=', 'section.sec_id')           
            ->select(
                'tt.*',
                'th.*',
                'course.prog_name as prog_code',
                'gradelvl.grad_name as gradelvl_desc',
                'section.sec_name as sec_desc',
                'program.dtype_desc as prog_desc'
            )
            // ->where('th.act_status', '=' , 1)
            ->where('tt.tuitemp_headerid','=',$id)
            ->where('tt.tuitemp_status', '=' , 1)
            ->orderBy('tt.tuitemp_id')
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

    
    public function getSubjectsFromRegistrar($course, $gradelvl, $sem, $curr)
    {
        try {
            $query = DB::table('def_curriculum_tags as dt')
            ->leftJoin('def_curriculum as dc', 'dt.currtag_currid', '=', 'dc.curr_id')
            ->leftJoin('def_subject as sj', 'dt.currtag_subjid', '=', 'sj.subj_id')
            ->select('dt.*', 'dc.*' , 'sj.*')
            ->when($course != 0, function ($q) use ($course) {
                $q->where('dc.curr_progid', $course);
            })
            ->when($gradelvl != 0, function ($q) use ($gradelvl) {
                $q->where('dt.currtag_gradelvl', $gradelvl);
            })
            ->when($sem != 0, function ($q) use ($sem) {
                $q->where('dt.currtag_sem', $sem);
            })
            ->where('dc.curr_id','=',$curr)
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

    public function getChargesTemplateHeader($curriculum, $sem, $program, $course, $gradelvl, $section)
    {
        try {

            $query = DB::table('def_accounts_tuition_header as th')
                ->where('th.act_status', 1)
                ->where(function($q) use ($curriculum) {
                    $q->where('th.act_curriculum', $curriculum)
                    ->orWhere('th.act_curriculum', 0);
                })
                ->where(function($q) use ($sem) {
                    $q->where('th.act_sem', $sem)
                    ->orWhere('th.act_sem', 0);
                })
                ->where(function($q) use ($program) {
                    $q->where('th.act_program', $program)
                    ->orWhere('th.act_program', 0);
                })
                ->where(function($q) use ($course) {
                    $q->where('th.act_course', $course)
                    ->orWhere('th.act_course', 0);
                })
                ->where(function($q) use ($gradelvl) {
                    $q->where('th.act_gradelvl', $gradelvl)
                    ->orWhere('th.act_gradelvl', 0);
                })
                ->where(function($q) use ($section) {
                    $q->where('th.act_section', $section)
                    ->orWhere('th.act_section', 0);
                })
                ->get();

            // 🟦 Collect getTuitionTemplate results
            $templateResults = [];

            foreach ($query as $item) {
                $id = $item->act_id;
                $templateResults[$id] = $this->getTuitionTemplate($id);  // 👍 correct way
            }

            $status = $query->isNotEmpty() ? 200 : 500;

            return [
                'data' => $query,
                'template' => $templateResults,
                'curriculum' => $curriculum,
                'sem' => $sem,
                'program' => $program,
                'gradelvl' => $gradelvl,
                'section' => $section,
                'status' => $status
            ];

        } catch (Exception $ex) {
            return [
                'data' => 'No Data',
                'template' => [],
                'curriculum' => $curriculum,
                'sem' => $sem,
                'program' => $program,
                'gradelvl' => $gradelvl,
                'section' => $section,
                'status' => 500
            ];
        }
    }

    public function getTotalCharges($curr, $sem, $program, $course, $gradelvl, $section, $enrid){
            $financedata = $this->getChargesTemplateHeader(
                $curr ?: 0,
                $sem ?: 0,
                $program ?: 0,
                $course ?: 0,
                $gradelvl ?: 0,
                $section ?: 0,
                $enrid ?: 0
            );

            /*
            |--------------------------------------------------------------------------
            | Flatten template data (same as Object.values + push in Vue)
            |--------------------------------------------------------------------------
            */
            $templatePricesData = [];
            foreach ($financedata['template'] as $template) {
                foreach ($template['data'] as $row) {
                    $templatePricesData[] = $row;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Get milestone data
            |--------------------------------------------------------------------------
            */
            $milestone = new RegistrarController();
            $milestonedata = $milestone->getMilestone(
               $enrid
            );

            /*
            |--------------------------------------------------------------------------
            | Merge milestone + template and compute totals
            |--------------------------------------------------------------------------
            */
            $overall_amount = 0;
            $subjects_amount = 0;
            $misc_amount = 0;
            $item_amount = 0;
            $lab_amount = 0;
            $lec_amount = 0;

            $mergedMilestones = [];

            foreach ($milestonedata as $ms) {

                $template = null;

                // Find matching template by subject ID
                foreach ($templatePricesData as $tp) {
                    if (
                        isset($tp->tuitemp_subjid, $ms->mi_subjid) &&
                        (int) $tp->tuitemp_subjid === (int) $ms->mi_subjid
                    ) {
                        $template = $tp;
                        break;
                    }
                }

                $total_price = 0;
                $mergedItem  = clone $ms; // copy object safely

                if ($template) {
                    // Merge template fields into milestone
                    foreach (get_object_vars($template) as $key => $value) {
                        $mergedItem->$key = $value;
                    }

                    // Compute from template
                    $total_price =
                        ((float) ($template->tuitemp_lec_price ?? 0) * (float) ($template->tuitemp_lec ?? 0)) +
                        ((float) ($template->tuitemp_lab_price ?? 0) * (float) ($template->tuitemp_lab ?? 0));

                    // compute lab and lec total
                    $lab_amount += ((float) ($template->tuitemp_lab_price ?? 0) * (float) ($template->tuitemp_lab ?? 0));
                    $lec_amount += ((float) ($template->tuitemp_lec_price ?? 0) * (float) ($template->tuitemp_lec ?? 0));

                    // Ensures template exists in UI
                    $mergedItem->tuitemp_id = $template->tuitemp_id;

                } else {
                    // Fallback to milestone rates
                    $total_price =
                        ((float) ($ms->subj_lec_rate ?? 0) * (float) ($ms->subj_lec_units ?? 0)) +
                        ((float) ($ms->subj_lab_rate ?? 0) * (float) ($ms->subj_lab_units ?? 0));

                    $lab_amount += ((float) ($ms->subj_lab_rate ?? 0) * (float) ($ms->subj_lab_units ?? 0));
                    $lec_amount += ((float) ($template->subj_lec_rate ?? 0) * (float) ($template->subj_lec_units ?? 0));
                }

                /*
                |--------------------------------------------------------------------------
                | APPLY CONDITION HERE, mi_tag 1 means exclude from total cost, already taken
                |--------------------------------------------------------------------------
                */
                if ((int) ($ms->mi_tag ?? 0) !== 1) {
                    $overall_amount += $total_price;
                    $subjects_amount += $total_price;
                }

                // Always attach total_price for UI
                // $mergedItem->total_price = $total_price;
                // $mergedMilestones[] = $mergedItem;
            }

            foreach ($templatePricesData as $tp) {
                if (empty($tp->tuitemp_subjid) && $tp->tuitemp_itemid) {
                    $overall_amount += (float) ($tp->tuitemp_price ?? 0);
                    $misc_amount += (float) ($tp->tuitemp_price ?? 0);
                }elseif (empty($tp->tuitemp_subjid) && $tp->tuitemp_custid) {
                    $overall_amount += (float) ($tp->tuitemp_price ?? 0);
                    $item_amount += (float) ($tp->tuitemp_price ?? 0);
                }else{
                    // Subject already counted in milestone loop
                }
            }

            return [
                'subjects_amount' => $subjects_amount,
                'misc_amount' => $misc_amount,
                'item_amount' => $item_amount,
                'lab_amount' => $lab_amount,
                'lec_amount' => $lec_amount,
                'overall_amount' => $overall_amount,
                'status' => 200
            ];
    }


    
}
