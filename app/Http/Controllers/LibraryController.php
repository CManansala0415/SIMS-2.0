<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use DateTime;

class LibraryController extends Controller
{
    public function getBooksAccession($limit, $offset, $search){
        if($search==204){

            if($limit == 0 && $offset == 0){
                $books = DB::table('def_library_book_accession as lba')
                ->select(  
                    'lba.*',
                )
                ->where('lba.lbrb_status', '=',  1)
                ->orderBy('lba.lbrb_accession_no','DESC')
                ->get();
            }else{
                $books = DB::table('def_library_book_accession as lba')
                ->select(  
                    'lba.*',
                )
                ->where('lba.lbrb_status', '=',  1)
                ->orderBy('lba.lbrb_accession_no','DESC')
                ->limit($limit)
                ->offset($offset)
                ->get();
            }
            
            $count = DB::table('def_library_book_accession as lba')
                ->select(  
                    'lba.*',
                )
                ->where('lba.lbrb_status', '=',  1)
                ->count();
        }else if ($search != 204 && ($limit == 1 && $offset == 1)){
            $books = DB::table('def_library_book_accession as lba')
            ->select(  
                'lba.*',
            )
            ->where('lba.lbrb_status', '=',  1)
            ->where(function($query) use ($search) {
                $query->where('lba.lbrb_call_no', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_title', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_author', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_edition', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_volume', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_pages', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_source', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_publisher', 'like',  '%' . $search .'%');
            })
            ->get();

            $count = DB::table('def_library_book_accession as lba')
            ->select(  
                'lba.*',
            )
            ->where('lba.lbrb_status', '=',  1)
            ->where(function($query) use ($search) {
                $query->where('lba.lbrb_call_no', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_title', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_author', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_edition', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_volume', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_pages', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_source', 'like',  '%' . $search .'%')
                ->orWhere('lba.lbrb_publisher', 'like',  '%' . $search .'%');
            })
            ->count();
        }
        else{
                $books = DB::table('def_library_book_accession as lba')
                        ->select(  
                            'lba.*',
                        )
                        ->where('lba.lbrb_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('lba.lbrb_call_no', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_title', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_author', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_edition', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_volume', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_pages', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_source', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_publisher', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_library_book_accession as lba')
                        ->select(  
                            'lba.*',
                        )
                        ->where('lba.lbrb_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('lba.lbrb_call_no', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_title', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_author', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_edition', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_volume', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_pages', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_source', 'like',  '%' . $search .'%')
                            ->orWhere('lba.lbrb_publisher', 'like',  '%' . $search .'%');
                        })
                        // ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $books,
            'count' => $count,
        ];

    }

    public function getBooksDdc($limit, $offset, $search){
        if($search==204){

            if($limit == 0 && $offset == 0){
                $books = DB::table('def_library_book_ddc as lbc')
                ->select(  
                    'lbc.*',
                )
                ->where('lbc.lbrc_status', '=',  1)
                ->orderBy('lbc.lbrc_id','DESC')
                ->get();
            }else{
                $books = DB::table('def_library_book_ddc as lbc')
                ->select(  
                    'lbc.*',
                )
                ->where('lbc.lbrc_status', '=',  1)
                ->orderBy('lbc.lbrc_id','DESC')
                ->limit($limit)
                ->offset($offset)
                ->get();
            }
            
            $count = DB::table('def_library_book_ddc as lbc')
                ->select(  
                    'lbc.*',
                )
                ->where('lbc.lbrc_status', '=',  1)
                ->count();
        }
        else{
                $books = DB::table('def_library_book_ddc as lbc')
                        ->select(  
                            'lbc.*',
                        )
                        ->where('lbc.lbrc_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('lbc.lbrc_ddc', 'like',  '%' . $search .'%')
                            ->orWhere('lbc.lbrc_desc', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_library_book_ddc as lbc')
                        ->select(  
                            'lbc.*',
                        )
                        ->where('lbc.lbrc_status', '=',  1)
                        ->where(function($query) use ($search) {
                            $query->where('lbc.lbrc_ddc', 'like',  '%' . $search .'%')
                            ->orWhere('lbc.lbrc_desc', 'like',  '%' . $search .'%');
                        })
                        // ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $books,
            'count' => $count,
        ];

    }

    public function addBooksDdc(Request $request){
        try{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');

            if($request->input('lbrc_mode') == 1){
                $primary = DB::table('def_library_book_ddc')
                ->where('lbrc_id','=', $request->input('lbrc_id'))
                ->update([
                    'lbrc_ddc' => $request->input('lbrc_ddc'),
                    'lbrc_desc' => $request->input('lbrc_desc'),
                    'lbrc_updatedby' => $request->input('lbrc_updatedby'),
                    'lbrc_dateupdated' => $date
                ]);
            }elseif($request->input('lbrc_mode') == 2){
                $primary = DB::table('def_library_book_ddc')->insert([
                    'lbrc_ddc' => $request->input('lbrc_ddc'),
                    'lbrc_desc' => $request->input('lbrc_desc'),
                    'lbrc_addedby' => $request->input('lbrc_addedby'),
                    'lbrc_dateadded' => $date
                ]);
            }else{
                $primary = DB::table('def_library_book_ddc')
                ->where('lbrc_id','=', $request->input('lbrc_id'))
                ->update([
                    'lbrc_status' => 0,
                    'lbrc_updatedby' => $request->input('lbrc_updatedby'),
                    'lbrc_dateupdated' => $date
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

    public function addBookInformation(Request $request){
        try{
            date_default_timezone_set('Asia/Manila');
            $date = date('Y-m-d H:i:s');

            if($request->input('lbrb_mode') == 1){
                $primary = DB::table('def_library_book_accession')
                ->where('lbrb_id','=', $request->input('lbrb_id'))
                ->update([
                    'lbrb_accession_no' => $request->input('lbrb_accession_no'),
                    'lbrb_call_no' => $request->input('lbrb_call_no'),
                    'lbrb_author' => $request->input('lbrb_author'),
                    'lbrb_title' => $request->input('lbrb_title'),
                    'lbrb_edition' => $request->input('lbrb_edition'),
                    'lbrb_volume' => $request->input('lbrb_volume'),
                    'lbrb_pages' => $request->input('lbrb_pages'),
                    'lbrb_publisher' => $request->input('lbrb_publisher'),
                    'lbrb_year' => $request->input('lbrb_year'),
                    'lbrb_source' => $request->input('lbrb_source'),
                    'lbrb_cost' => $request->input('lbrb_cost'),
                    'lbrb_datereceived' => $request->input('lbrb_datereceived'),
                    'lbrb_updatedby' => $request->input('lbrb_updatedby'),
                    'lbrb_dateupdated' => $date
                ]);
            }elseif($request->input('lbrb_mode') == 2){
                $primary = DB::table('def_library_book_accession')->insert([
                    'lbrb_accession_no' => $request->input('lbrb_accession_no'),
                    'lbrb_call_no' => $request->input('lbrb_call_no'),
                    'lbrb_author' => $request->input('lbrb_author'),
                    'lbrb_title' => $request->input('lbrb_title'),
                    'lbrb_edition' => $request->input('lbrb_edition'),
                    'lbrb_volume' => $request->input('lbrb_volume'),
                    'lbrb_pages' => $request->input('lbrb_pages'),
                    'lbrb_publisher' => $request->input('lbrb_publisher'),
                    'lbrb_year' => $request->input('lbrb_year'),
                    'lbrb_source' => $request->input('lbrb_source'),
                    'lbrb_cost' => $request->input('lbrb_cost'),
                    'lbrb_datereceived' => $request->input('lbrb_datereceived'),
                    'lbrb_addedby' => $request->input('lbrb_addedby'),
                    'lbrb_dateadded' => $date
                ]);
            }else{
                $primary = DB::table('def_library_book_accession')
                ->where('lbrb_id','=', $request->input('lbrb_id'))
                ->update([
                    'lbrb_status' => 0,
                    'lbrb_updatedby' => $request->input('lbrb_updatedby'),
                    'lbrb_dateupdated' => $date
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

    public function addBorrowedBooks(Request $request){
        //date time saving last to fix naten
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');
        $data = $request->all();
        
        foreach ($data as $key => $value) {
            try{
                $secondary = DB::table('def_library_book_borrow')->insert([
                    'lbrr_personid' => $value['lbrr_personid'],
                    'lbrr_cardid' => $value['lbrr_cardid'],
                    'lbrr_enrid' => $value['lbrr_enrid'],
                    'lbrr_accessionid' => $value['lbrr_accessionid'],
                    'lbrr_bookid' => $value['lbrr_bookid'],
                    'lbrr_addedby' => $value['lbrr_user'],
                    'lbrr_day_borrowed' => $value['lbrr_day_borrowed'],
                    'lbrr_dateborrowed' => $date,
                    'lbrr_dateadded' => $date
                ]);
            }
            catch (Exception $ex) {
                return $data = [
                    'status' => 500,
                ];
            }
        }

        return $data = [
            'data' => $request,
            'status' => 200,
        ];
    }

    public function getBorrowedBooks($limit, $offset, $search){
        if($search==204){

            if($limit == 0 && $offset == 0){
                $books = DB::table('def_library_book_borrow as brr')
                ->leftJoin('def_employee as emp', 'brr.lbrr_addedby', '=', 'emp.emp_id')
                ->leftJoin('def_person as per', 'brr.lbrr_personid', '=', 'per.per_id')
                ->leftJoin('def_library_book_accession as lbc', 'brr.lbrr_bookid', '=', 'lbc.lbrb_id')
                ->select(  
                    'brr.*',
                    'emp.*',
                    'per.*',
                    'lbc.*'
                )
                ->where('brr.lbrr_status', '=',  1)
                ->orderBy('brr.lbrr_id','DESC')
                ->get();
            }
            else{
                $books = DB::table('def_library_book_borrow as brr')
                ->leftJoin('def_employee as emp', 'brr.lbrr_addedby', '=', 'emp.emp_id')
                ->leftJoin('def_person as per', 'brr.lbrr_personid', '=', 'per.per_id')
                ->leftJoin('def_library_book_accession as lbc', 'brr.lbrr_bookid', '=', 'lbc.lbrb_id')
                ->select(  
                    'brr.*',
                    'emp.*',
                    'per.*',
                    'lbc.*'
                )
                ->where('brr.lbrr_status', '=',  1)
                ->orderBy('brr.lbrr_id','DESC')
                ->limit($limit)
                ->offset($offset)
                ->get();
            }
            
            $count = DB::table('def_library_book_borrow as brr')
                ->leftJoin('def_employee as emp', 'brr.lbrr_addedby', '=', 'emp.emp_id')
                ->leftJoin('def_person as per', 'brr.lbrr_personid', '=', 'per.per_id')
                ->leftJoin('def_library_book_accession as lbc', 'brr.lbrr_bookid', '=', 'lbc.lbrb_id')
                ->select(  
                    'brr.*',
                    'emp.*',
                    'per.*',
                    'lbc.*'
                )
                ->where('brr.lbrr_status', '=',  1)
                ->count();
        }
        else if (($search == 200) && ($limit == 0 && $offset == 0)){ // means all iba to sa normal na 1, 1
            $books = DB::table('def_library_book_borrow as brr')
                    ->leftJoin('def_employee as emp', 'brr.lbrr_addedby', '=', 'emp.emp_id')
                    ->leftJoin('def_person as per', 'brr.lbrr_personid', '=', 'per.per_id')
                    ->leftJoin('def_library_book_accession as lbc', 'brr.lbrr_bookid', '=', 'lbc.lbrb_id')
                    ->select(  
                        'brr.*',
                        'emp.*',
                        'per.*',
                    'lbc.*'
                    )
                    ->where('brr.lbrr_status', '=',  1)
                    ->get();
            $count =  DB::table('def_library_book_borrow as brr')
                    ->leftJoin('def_employee as emp', 'brr.lbrr_addedby', '=', 'emp.emp_id')
                    ->leftJoin('def_person as per', 'brr.lbrr_personid', '=', 'per.per_id')
                    ->leftJoin('def_library_book_accession as lbc', 'brr.lbrr_bookid', '=', 'lbc.lbrb_id')
                    ->select(  
                        'brr.*',
                        'emp.*',
                        'per.*',
                        'lbc.*'
                    )
                    ->where('brr.lbrr_status', '=',  1)
                    ->count();       
        }
        else{
            $books = DB::table('def_library_book_borrow as brr')
                        ->leftJoin('def_employee as emp', 'brr.lbrr_addedby', '=', 'emp.emp_id')
                        ->leftJoin('def_person as per', 'brr.lbrr_personid', '=', 'per.per_id')
                        ->leftJoin('def_library_book_accession as lbc', 'brr.lbrr_bookid', '=', 'lbc.lbrb_id')
                        ->select(  
                            'brr.*',
                            'emp.*',
                            'per.*',
                            'lbc.*'
                        )
                        ->where('brr.lbrr_status', '=',  1)
                        ->orderBy('brr.lbrr_id','DESC')
                        ->where(function($query) use ($search) {
                            $query->where('brr.lbrr_accessionid', 'like',  '%' . $search .'%')
                            ->orWhere('brr.lbrr_bookid', 'like',  '%' . $search .'%')                            
                            ->orWhere('lbc.lbrb_author', 'like',  '%' . $search .'%')
                            ->orWhere('lbc.lbrb_title', 'like',  '%' . $search .'%')
                            ->orWhere('per.per_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('per.per_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('per.per_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('per.per_suffixname', 'like',  '%' . $search .'%');
                        })
                        ->limit($limit)->offset($offset)
                        ->get();
            $count =  DB::table('def_library_book_borrow as brr')
                        ->leftJoin('def_employee as emp', 'brr.lbrr_addedby', '=', 'emp.emp_id')
                        ->leftJoin('def_person as per', 'brr.lbrr_personid', '=', 'per.per_id')
                        ->leftJoin('def_library_book_accession as lbc', 'brr.lbrr_bookid', '=', 'lbc.lbrb_id')
                        ->select(  
                            'brr.*',
                            'emp.*',
                            'per.*',
                            'lbc.*'
                        )
                        ->where('brr.lbrr_status', '=',  1)
                        ->orderBy('brr.lbrr_id','DESC')
                        ->where(function($query) use ($search) {
                            $query->where('brr.lbrr_accessionid', 'like',  '%' . $search .'%')
                            ->orWhere('brr.lbrr_bookid', 'like',  '%' . $search .'%')
                            ->orWhere('lbc.lbrb_author', 'like',  '%' . $search .'%')
                            ->orWhere('lbc.lbrb_title', 'like',  '%' . $search .'%')
                            ->orWhere('per.per_firstname', 'like',  '%' . $search .'%')
                            ->orWhere('per.per_middlename', 'like',  '%' . $search .'%')
                            ->orWhere('per.per_lastname', 'like',  '%' . $search .'%')
                            ->orWhere('per.per_suffixname', 'like',  '%' . $search .'%');
                        })
                        // ->limit($limit)->offset($offset)
                        ->count();       
        }

        return $data = [
            'data' => $books,
            'count' => $count,
        ];
    }

    public function getBorrowedBooksBy($cardid, $personid, $enrid){
        $books = DB::table('def_library_book_borrow as brr')
                ->leftJoin('def_employee as emp', 'brr.lbrr_addedby', '=', 'emp.emp_id')
                ->leftJoin('def_person as per', 'brr.lbrr_personid', '=', 'per.per_id')
                ->leftJoin('def_library_book_accession as lbc', 'brr.lbrr_bookid', '=', 'lbc.lbrb_id')
                ->leftJoin('def_library_card_issue as lbd', 'brr.lbrr_cardid', '=', 'lbd.lbrd_id')
                ->select(  
                    'brr.*',
                    'emp.*',
                    'per.*',
                'lbc.*'
                )
                ->where('lbd.lbrd_id', '=',  $cardid)
                ->where('brr.lbrr_personid', '=',  $personid)
                ->where('brr.lbrr_enrid', '=',  $enrid)
                ->where('brr.lbrr_returned', '=',  0)
                ->get();
        return $books;
    }
    
    public function updateBorrowedBooks(Request $request){
        //date time saving last to fix naten
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');
        
        try{
            $primary = DB::table('def_library_book_borrow')
            ->where('lbrr_id','=', $request->input('lbrr_id'))
            ->update([
                'lbrr_datereturned' => $request->input('lbrr_datereturned'),
                'lbrr_day_borrowed' => $request->input('lbrr_day_borrowed'),
                'lbrr_penalty' => $request->input('lbrr_penalty'),
                'lbrr_acceptedby' => $request->input('lbrr_user'),
                'lbrr_dateaccepted' => $date,
                'lbrr_returned' => 1,
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

    public function getLibraryCardIssue($personid, $enrid, $active){

        if($active == 0){
            $cards = DB::table('def_library_card_issue as lbd')
                ->leftJoin('users as user', 'lbd.lbrd_issuedby', '=', 'user.id')
                ->leftJoin('def_employee as emp', 'user.id', '=', 'emp.emp_accid')
                ->leftJoin('def_person as per', 'lbd.lbrd_personid', '=', 'per.per_id')
                ->select(  
                    'lbd.*',
                    'emp.*',
                    'per.per_firstname',
                    'per.per_middlename',
                    'per.per_lastname',
                    'per.per_suffixname',
                )
                ->where('lbd.lbrd_personid','=', $personid)
                ->where('lbd.lbrd_enrid','=', $enrid)
                ->orderBy('lbd.lbrd_id','DESC')
                ->get();
        }else{
            $cards = DB::table('def_library_card_issue as lbd')
            ->leftJoin('users as user', 'lbd.lbrd_issuedby', '=', 'user.id')
            ->leftJoin('def_employee as emp', 'user.id', '=', 'emp.emp_accid')
            ->leftJoin('def_person as per', 'lbd.lbrd_personid', '=', 'per.per_id')
            ->select(  
                'lbd.*',
                'emp.*',
                'per.per_firstname',
                'per.per_middlename',
                'per.per_lastname',
                'per.per_suffixname',
            )
            ->where('lbd.lbrd_personid','=', $personid)
            ->where('lbd.lbrd_enrid','=', $enrid)
            ->where('lbd.lbrd_status','=', 1)
            ->get();
        }
       


        return $cards; 
    }

    public function deactivateLibraryCard(Request $request){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');
        try{
            $primary = DB::table('def_library_card_issue')
            ->where('lbrd_id','=', $request->input('lbrd_id'))
            ->update([
                'lbrd_status' => 0,
                'lbrd_deactivatedby' => $request->input('lbrd_user'),
                'lbrd_datedeactivated' => $date
            ]);

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

    public function addLibraryCard(Request $request){
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d H:i:s');
        $code = 'LB-'.$request->input('lbrd_dateissued').$request->input('lbrd_enrid');

        try{
            $primary = DB::table('def_library_card_issue')->insert([
                'lbrd_cardno' => $request->input('lbrd_cardno'),
                'lbrd_cardcode' => $code,
                'lbrd_personid' => $request->input('lbrd_personid'),
                'lbrd_enrid' => $request->input('lbrd_enrid'),
                'lbrd_issuedby' => $request->input('lbrd_issuedby'),
                'lbrd_dateissued' => $request->input('lbrd_dateissued'),
                'lbrd_addedby' => $request->input('lbrd_user'),
                'lbrd_dateadded' => $date
            ]);

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
