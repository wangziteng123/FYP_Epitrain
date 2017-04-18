<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Support\Facades\Input;
use Excel;
use Exception;

use Illuminate\Http\Request;

use App\Http\Requests;
use Mail;

use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_IValueBinder;
use PHPExcel_Cell_DefaultValueBinder;


class MyValueBinder extends PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
    {
        public function bindValue(PHPExcel_Cell $cell, $value = null)
        {
            if (is_numeric($value))
            {
                $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);

                return true;
            }

            // else return default behavior
            return parent::bindValue($cell, $value);
        }
    }
/**
 * UserController Class used for users management such as creation of users, viewing of users
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::all();

        return \View::make('usermanage.updateInfo', compact('users'));
    }

    public function viewAllUsers(){
        //
        $users = User::paginate(15);

        return \View::make('usermanage.viewAllUsers', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $name = $request->input('name');
        $email= $request->input('email');
        $password = $request->input('password');
        
        $user = User::find($id);
        $user->name = $name;
        if($user->save()) {
            return redirect('createUser')->with('success', "User successfully created!");
        } else {
            return redirect('createUser')->with('failure', "Please fill in all the details!");
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
        ]);
        $user = new User;
        
        $user->name = $request->name;
        $user->email = $request->email;
        $random_password = $this->rand_string(16);
        $user->password = bcrypt($random_password);
				
				$isAdmin = $request->input('make-admin');
				if($isAdmin !== "0") {
					$user->isAdmin = 1;
				} else {
					$user->isAdmin = 0;
				}
        if($user->save()) {
						Mail::send('emails.UserCreated',
							array(
									'name' => $user->name,
									'username' => $user->email,
									'password' => $random_password
							), function($message) use ($user)
						{
								$message->from('cavetzii@gmail.com','Epitrain Elearning Platform Robot');
								$message->to($user->email, $user->name)->subject('Welcome to Epitrain');
						});
            return redirect('createUser')->with('success', "User successfully created!");
        } else {
            return redirect('createUser')->with('failure', "Please fill in all the details!");
        }
    }
    
    
    /**
    *to create many users in a single csv file in a correct format. Takes in the input name, email and isAdmin.
    *
    *@param Request $request
    *
    * @return String $success
    */
    public function csvStore(Request $request){
        $mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
        if(in_array($_FILES['filefield']['type'],$mimes)){
            $csvFile = $request->get('filefield');
        
            $missingEmailField = "";
            $success = "Users successfully created!";
            $atLeastOneSuccess = false;
            $errorDuplicateEmail = array();
            $errorInvalidEmail = array();
            $myValueBinder = new MyValueBinder;

            if(Input::hasFile('filefield')){
                $path = Input::file('filefield')->getRealPath();
                $reader = Excel::setValueBinder($myValueBinder)->load(Input::file('filefield'));

                $data = Excel::load($path, function($reader) {
                })->get();
                if(!empty($data) && $data->count()){

                    foreach ($data as $key => $value) {
                    
                        $emailExists = \DB::table('users') 
                            -> where ('email', '=', $value->email)
                            -> get();
                        if($emailExists != null){
                            array_push($errorDuplicateEmail, $value->email);
                        } else{
                            if(empty($value->email) || empty($value->name)){
                                $missingEmailField = "Missing name/email field(s) or Wrong format. Please ensure your CSV follows the proper format with no empty fields.";
                            }else if(!(filter_var($value->email, FILTER_VALIDATE_EMAIL))) {
                                array_push($errorInvalidEmail, $value->email);
                                //dd($errorInvalidEmail);
                            } else{
                                
                                $user = new User;
                                $user->name = $value->name;
                                $user->email = $value->email;
                                $random_password = $this->rand_string(16);
                                $user->password = bcrypt($random_password);
                                if($value->isadmin == 1){
                                    $user->isadmin = 1;
                                } else{
                                    $user->isadmin = 0;
                                }
                                if($user->save()) {
                                    Mail::send('emails.UserCreated',
                                        array(
                                                'name' => $user->name,
                                                'username' => $user->email,
                                                'password' => $random_password
                                        ), function($message) use ($user)
                                    {
                                        $message->from('cavetzii@gmail.com','Epitrain Elearning Platform Robot');
                                        $message->to($user->email, $user->name)->subject('Welcome to Epitrain');
                                    });
                                    $atLeastOneSuccess = true;
                                } 
                            }
                        }
                        
                    }
                    if($atLeastOneSuccess){
                        if(!empty($missingEmailField)){
                            return \View::make('usermanage.create')->with(compact('success','errorInvalidEmail','errorDuplicateEmail', 'missingEmailField'));
                        } else{
                            return \View::make('usermanage.create')->with(compact('success','errorInvalidEmail','errorDuplicateEmail'));
                        }
                        //return redirect('createUser')->with(compact('success','errorInvalidEmail','errorDuplicateEmail'));
                    } else{
                        if(!empty($missingEmailField)){
                            return \View::make('usermanage.create')->with(compact('errorInvalidEmail','errorDuplicateEmail', 'missingEmailField'));
                        } else{
                            return \View::make('usermanage.create')->with(compact('errorInvalidEmail','errorDuplicateEmail'));
                        }
                        //return \View::make('usermanage.create')->with(compact('errorInvalidEmail','errorDuplicateEmail', 'missingEmailField'));
                        //return redirect('createUser')->with(compact('errorInvalidEmail', 'errorDuplicateEmail'));
                    }

                } else{
                    //return redirect('createUser')->with('emptyFile', "Provided an empty/invalid format file");
                }
            }
        } else {
            
        }

		return \View::make('usermanage.create')-> with('wrongFileFormat', "Please provide only a CSV file.");
            // reader methods

        
    }
    /**
    *create random password to give to user account that are created by admin.
    *
    *@param int $length
    *
    * @return String $str
    */
	function rand_string( $length ) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    $size = strlen( $chars );
	    for( $i = 0; $i < $length; $i++ ) {
	        $str .= $chars[ rand( 0, $size - 1 ) ];
	    }

	    return $str;
	}



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        if(!($request->get('clause'))){
            return redirect('/update')->with('status', 'Please check the Terms & Conditions box.');
        }

        $error = "";
        $this->validate($request, [
        'name' => 'required'
        ]);
        try{
            $name = $request->input('name');
            $email= $request->input('email');
            $password = $request->input('password');
            $confirmPassword = $request->input('password_confirmation');
            $currentPasswordInput = $request->input('currentPassword'); // current password that is retrieve from the form


             $user = User::find($id);
             $currentPassword = $request->input('passwordCheck'); //hashed password from db


              if(password_verify($currentPasswordInput,$currentPassword) && $password == $confirmPassword && $password != "" && $confirmPassword !="" && strcmp($user->name,$name) !== 0){

                    $user->password = bcrypt($request->input('password'));
                    $user->name = $request->input('name');
                    $error = "changeAll";
                    $data = array('error'  => $error);

                    } else if (password_verify($currentPasswordInput,$currentPassword) && $password == $confirmPassword && $password != "" && $confirmPassword !=""){
                         $user->password = bcrypt($request->input('password'));;

                         $error = "changePW";
                         $data = array('error'  => $error);
                    } else if (password_verify($currentPasswordInput,$currentPassword) && $password == $confirmPassword && $password == "" && $confirmPassword =="" && strcmp($user->name,$name) !== 0){
                        $user->name =$name;
                        $error = "changeName";
                        $data = array('error'  => $error);

                    } else if (password_verify($currentPasswordInput,$currentPassword) && $password == $confirmPassword && $password == "" && strcmp($user->name,$name) == 0){
                        $user->name =$name;
                        $error = "noChange";
                        $data = array('error'  => $error);

                    }
                     $user->save();

              }
              catch(\Exception $e){
                          $error = "failed";
                          $data = array(
                              'error'  => $error
                          );
              }
              finally{
                    if($error == "changeAll"){
                        flash('Name and Password Changed Successfully!', 'success');
                    }

                    else if($error =="changePW"){
                         flash('Password Changed Successfully!', 'success');
                    }
                    else if($error =="changeName"){
                         flash('Name Changed Successfully!', 'success');
                    }
                    else if ($error =="noChange") {
                        flash('No changes made to personal info', 'warning');
                    }
                    else{
                         flash('Update failed! Incorrect current password or mismatch confirmation of new password', 'danger');
                    }
                    return redirect('update');

              }


        }


}
