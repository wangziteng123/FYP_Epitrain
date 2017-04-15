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
            
            
            $success = "Admin Email Successfully changed";
            return \View::make('admin.admin')->with('success',$success);
        } else {
            $error = "Email entered is invalid";
        }
        
        if(!empty($error)){
            return \View::make('admin.admin')->with('error',$error);
        }
    }
    
}
