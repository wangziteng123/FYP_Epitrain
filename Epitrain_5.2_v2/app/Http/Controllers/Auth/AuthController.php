<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use App\ActivationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use DB;
use Log;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    protected $redirectAfterLogout = '/login';
    
    protected $activationService;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(ActivationService $activationService)
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->activationService = $activationService;
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $user = $this->create($request->all());

        $this->activationService->sendActivationMail($user);

        return redirect('/login')->with('status', 'We sent you an activation code. Check your email.');
    }
    public function logout(Request $request) {
        DB::table('sessions')->where('user_id', auth()->user()->id) //change login status in DB to logged out
            ->update(
                ['loggedIn' => 0]
            );
        Auth::logout();
        Session::flush();
        return redirect('/');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    /**
     * This method handles what happens after users keyed in the correct username and password
     *
     * @param  Request  $request
     * @param  User  $user
     * @return chosen page or login page if encounters error
     */
    public function authenticated(Request $request, $user) 
        {
            $user_id = auth()->user()->id;
            $session_id = \Session::getId();
            $last_activity = time();

            $user_record = DB::table('sessions')->where('user_id', $user_id)->first();
            if ($user_record == null) {
                DB::table('sessions')->insert(
                    ['user_id' => $user_id, 'id' => $session_id,'last_activity' => $last_activity, 'loggedIn' => 1]
                );
            } else {
            	if ($user_record->loggedIn == 0) {
            		DB::table('sessions')->where('user_id', $user_id)
	                ->update(
	                    ['user_id' => $user_id, 'id' => $session_id,'last_activity' => $last_activity, 'loggedIn' => 1]
	                );
            	} else {
            		auth()->logout();
            		return back()->with('message', 'Someone is using your account. You cannot login until the person logs out. Please contact admin if you need help.');
            	}
                
            }
            //redirect to home page or the page that corresponds to the URL users typed in
            return redirect()->intended($this->redirectPath()); 
        }

    public function activateUser($token)
    {
        if ($user = $this->activationService->activateUser($token)) {
            auth()->login($user);
            return redirect($this->redirectPath());
        }
        abort(404);
    }

}
