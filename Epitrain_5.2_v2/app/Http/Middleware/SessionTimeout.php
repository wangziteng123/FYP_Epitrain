<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Log;
use Auth;
use DB;

class SessionTimeout {

    //protected $timeout = 1800; //session timeout after 30 mins

    public function __construct(Store $session){
        //$request->session() = $session;
    }
    /**
     * Handle an incoming request. equest to this handler is received every time user clicks anything/refresh a page
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sessionTime = \DB::table('sessiontime')
            -> orderBy('session_id', 'DESC')
            -> first();
        if (Auth::check()) { //check if user is logged in
            $user_record = DB::table('sessions')->where('user_id', auth()->user()->id)->first();
            if ($user_record != null) { 
            	// if user's last activity was at least $sessionTime->session_time minutes ago, logs him/her out
                if (time() - $user_record->last_activity > $sessionTime->session_time) { 
                    auth()->logout();
                    $sTime = $sessionTime->session_time/60;
                    return redirect('login')->with('message', 'You were inactive for more than '.$sTime.' minutes. This platform will automatically log you out after '.$sTime.' minutes.');
                } else {
                // if not, update user's latest activity
                    DB::table('sessions')->where('user_id', auth()->user()->id)
                    ->update(
                        ['last_activity' => time()]
                    );
                }
            }   
        }
        return $next($request); //continue to process next request
    }

}