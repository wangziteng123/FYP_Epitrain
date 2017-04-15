<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    //
     public function index()
    {
        //
        return \View::make('admin.admin');
    }
    
    public function changeEmail(Request $request){
        $changeAdminEmail = $request->get('adminEmail');
        $error = "";
        $emailAdd = \DB::table('adminemail')
            -> orderBy('email_id', 'DESC')
            -> first();
        $currentEmailAddress = $emailAdd->email;
        
        if(empty($changeAdminEmail)){
            $error = "Email field is blank";
        } else if($changeAdminEmail == $currentEmailAddress){
            $error = "Email entered is the same";
        } else if((filter_var($changeAdminEmail, FILTER_VALIDATE_EMAIL))) {
            DB::table('adminemail') ->insert(
                ['email' => $changeAdminEmail]
            );
            
            
            $success = "Admin Email successfully changed";
            return \View::make('admin.admin')->with('success',$success);
        } else {
            $error = "Email entered is invalid";
        }
        
        if(!empty($error)){
            return \View::make('admin.admin')->with('error',$error);
        }
    }
    
    public function changeSessionTimeout(Request $request){
        $timing = $request->get('sessionTime');
        $checkTiming = ctype_digit($timing);
        
        $sessionError ="";
        $cSessionTiming = \DB::table('sessiontime')
            -> orderBy('session_id', 'DESC')
            -> first();
        $currentSessionTiming = $cSessionTiming->session_time;
        
        if($checkTiming){
            $convertedTiming = (int)$timing;
            if($convertedTiming*60 == $currentSessionTiming){
                $sessionError = "Session Timeout entered is the same";
            } else if(empty($timing)){
                $sessionError = "Session Timeout field is blank";
            } else{
                DB::table('sessiontime') ->insert(
                    ['session_time' => $timing*60]
                );
            
                $sessionSuccess = "Session Timeout successfully changed";
                return \View::make('admin.admin')->with('success',$sessionSuccess);
            }
        } else {
            if(!empty($timing)){
                $sessionError = "Session Timeout entered is not in minutes";
            } else{
                $sessionError = "Session Timeout field is blank";
            }
        } 
        
        if(!empty($sessionError)){
            return \View::make('admin.admin')->with('error',$sessionError);
        }
    }
    
}
