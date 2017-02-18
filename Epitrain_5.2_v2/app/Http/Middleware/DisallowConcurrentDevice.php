<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Log;
use DB;
use Auth;

class DisallowConcurrentDevice {

    public function __construct(Store $session){
        //$request->session() = $session;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user_record = DB::table('sessions')->where('user_id', auth()->user()->id)->first();
            if ($user_record != null) {
                if (strcmp($user_record->old_id,\Session::getId()) == 0 && $user_record->loggedIn == 1) {
                    auth()->logout();
                    flash('Someone logged in to your account on another browser/device. You were automatically logged out.', 'danger');

                }
            }  
        } 

        //$isLoggedIn ? $request->session()->put('lastActivityTime', time()) : $request->session()->forget('lastActivityTime');
        if (strcmp(session()->get('flash_notification.message'),'Someone logged in to your account on another browser/device. You were automatically logged out.') == 0) {
            flash('Someone logged in to your account on another browser/device. You were automatically logged out.', 'danger');
        }
        //Log::warning(session()->has('flash_notification.message'). " here");
        return $next($request);
    }

}