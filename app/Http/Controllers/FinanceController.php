<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class FinanceController extends Controller
{
    public function getSetSeries($id){

        $series = DB::table('def_accounts_series')
                ->where('sr_cashierid', '=',  $id)
                ->where('sr_status', '=',  1)
                ->get();
        return $series;

    }
}
