<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Log;
use DB;
use Auth;

class DisallowConcurrentDevice {

    //protected $timeout = 1800; //session timeout after 30 mins

    public function __construct(Store $session){
        //$request->session() = $session;
    }
    /**
     * Handle an incoming request. Request to this handler is received every time user clicks anything/refresh a page
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	// constantly changes loggedIn status to 1 if user clicks anything to prevent them from exploiting session timeout auto-logout
        if (Auth::check()) { 
            $user_record = DB::table('sessions')->where('user_id', auth()->user()->id)->first();
            if ($user_record != null) {
            	if(strcmp($user_record->id,\Session::getId()) !== 0) {
            		auth()->logout();
                    return redirect('login')->with('message', 'Someone is using your account. You cannot login until the person logs out. Please contact admin if you need help.');
            	}
            }
        }

        // get the list of users who are inactive for more than 30 minutes & change their login status to match with session timeout logout and prevent them from being blocked out of their accounts.
        $user_records = DB::table('sessions')->where([
        	['loggedIn', '=', 1],
        	['last_activity', '<', time() - 1800],
        	])->get();
        if ($user_records != null) {
            foreach ($user_records as $user_record) {
                DB::table('sessions')->where('user_id', $user_record->user_id)
                ->update(
                    ['loggedIn' => 0]
                );
            }
        }  

        return $next($request);
    }

}