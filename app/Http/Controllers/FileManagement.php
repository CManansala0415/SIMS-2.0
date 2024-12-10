<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FileManagement extends Controller
{
    public function uploadProfile(Request $request) {
        // $pathToFile = $request->file('image')->store('images', 'public');
        $pathToFile = $request->file('image');
        $pathToFile->storeAs(
            'profiles',
            $pathToFile->getClientOriginalName(),
            'public'
        );
        // return $pathToFile;
        if($pathToFile){
            return $data = [
                'status' => 200,
                'link' =>  $pathToFile->getClientOriginalName(),
            ];
        }else{
            return $data = [
                'status' => 500,
            ];
        }
    } 
    public function uploadLink(Request $request) {
        $s1 = DB::table('def_person')
                ->where('per_id','=', $request['personid'])
                ->update([
                    "per_profile" => $request['profile'],
                ]);
            
            return $data = [
                'status' => 200,
            ];
    } 

    public function addMedicalFilesImage(Request $request, $location) {
        // $pathToFile = $request->file('image')->store('images', 'public');
        $address = 'clinic/'.$location.'/';
        $pathToFile = $request->file('image');
        $pathToFile->storeAs(
            $address,
            $pathToFile->getClientOriginalName(),
            'public'
        );

        // return $pathToFile;
        if($pathToFile){
            return $data = [
                'status' => 200,
                'link' =>  $pathToFile->getClientOriginalName(),
            ];
        }else{
            return $data = [
                'status' => 500,
            ];
        }
    } 

    public function uploadMedicalFileLink(Request $request) {
        //date time saving last to fix naten
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s', time());
        $data = $request->all();

        try{
            //create header then items
            $primary = DB::table('def_clinic_medical_files')->insert([
                'clmf_headerid' => $request->input('clmf_headerid'),
                'clmf_filename' => $request->input('clmf_filename'),
                'clmf_resulttype' => $request->input('clmf_resulttype'),
                'clmf_addedby' =>$request->input('clmf_user'),
                'clmf_dateadded' => $date,
            ]);

        }
        catch (Exception $ex) {
            return $data = [
                'status' => 500,
            ];
        }
    } 

    
}
