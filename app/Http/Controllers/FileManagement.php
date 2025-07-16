<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File; 

class FileManagement extends Controller
{
    public function uploadProfile(Request $request, $existing, $folder){
        // $pathToFile = $request->file('image')->store('images', 'public');
        $linkingfolder = '';
        if($folder == 'Alumni'){
            $linkingfolder = '/storage/alumni/';
            $foldername = 'alumni';
        }else{
            $linkingfolder = '/storage/profiles/';
            $foldername = 'profiles';
        }

        if($existing != 0){
            $path = public_path().$linkingfolder.$existing;
            unlink($path);
        }

        $pathToFile = $request->file('image');
        $pathToFile->storeAs(
            $foldername,
            $pathToFile->getClientOriginalName(),
            'public'
        );

        if($pathToFile){
            return $data = [
                'old_profile' => $existing,
                'status' => 200,
                'link' =>  $pathToFile->getClientOriginalName(),
            ];
        }else{
            return $data = [
                'status' => 500,
            ];
        }

        // legacy code
        // if($existing != 0){
        //     $path = public_path().'/storage/profiles/'.$existing;
        //     unlink($path);
        // }

        // $pathToFile = $request->file('image');
        // $pathToFile->storeAs(
        //     'profiles',
        //     $pathToFile->getClientOriginalName(),
        //     'public'
        // );
        // // return $pathToFile;
        // if($pathToFile){
        //     return $data = [
        //         'old_profile' => $existing,
        //         'status' => 200,
        //         'link' =>  $pathToFile->getClientOriginalName(),
        //     ];
        // }else{
        //     return $data = [
        //         'status' => 500,
        //     ];
        // }
    } 
    public function uploadLinkProfile(Request $request) {
        if($request['type'] == 'Alumni'){
            $s1 = DB::table('server_archive_persons')
                ->where('arc_personid','=', $request['personid'])
                ->update([
                    "arc_profile" => $request['profile'],
            ]);
        }else{
            $s1 = DB::table('def_person')
                ->where('per_id','=', $request['personid'])
                ->update([
                    "per_profile" => $request['profile'],
            ]);
        }
        
        return $data = [
            'status' => 200,
        ];
    } 

    public function uploadSignature(Request $request, $existing) {
        // $pathToFile = $request->file('image')->store('images', 'public');

        if($existing != 0){
            $path = public_path().'/storage/signatures/'.$existing;
            unlink($path);
        }

        $pathToFile = $request->file('image');
        $pathToFile->storeAs(
            'signatures',
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
    public function uploadLinkSignature(Request $request) {
        $s1 = DB::table('def_person')
            ->where('per_id','=', $request['personid'])
            ->update([
                "per_signature" => $request['signature'],
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
