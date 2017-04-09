<?php

namespace App\Http\Controllers;
use App\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use Mail;


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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
public function update(Request $request, $id)
        {

          //  return \View::make('usermanage.updateInfo', compact('users'));
           // return redirect()->back();
    //return redirect('home');
          //  return redirect('home');
            //return \View::make('usermanage.updateInfo');
            //$validation = Validator::make($input, User::$rules);

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
                     //echo $confirmPassword + " confirm pw";

                     $user = User::find($id);
                    $currentPassword = $request->input('passwordCheck'); //hashed password from db

                     //$user->name = $name;
                    //$currentPassword = bcrypt('123456');


                    if(password_verify($currentPasswordInput,$currentPassword) && $password == $confirmPassword && $password != "" && $confirmPassword !="" && strcmp($user->name,$name) !== 0){
                     //echo "entered";
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

             } catch(\Exception $e){
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
                      //return redirect('home');
                      //return \View::make('usermanage.updateInfo', array('error' => $error));


                      }




        }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
