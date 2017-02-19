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
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            DB::table('sessions')->where('user_id', auth()->user()->id)
            ->update(
                ['loggedIn' => 1]
            );
        }
        $user_records = DB::table('sessions')->where('loggedIn', '=', 1)->get();
        //print_r($user_records);
        if ($user_records != null) {
            foreach ($user_records as $user_record) {
                if (time() - $user_record->last_activity > 1800) {
                    DB::table('sessions')->where('user_id', $user_record->user_id)
                    ->update(
                        ['loggedIn' => 0]
                    );
                } 
            }
        }  

        /*if (strcmp(session()->get('flash_notification.message'),'Someone logged in to your account on another browser/device. You were automatically logged out.') == 0) {
            flash('Someone logged in to your account on another browser/device. You were automatically logged out.', 'danger');
        }*/
        //Log::warning(session()->has('flash_notification.message'). " here");
        return $next($request);
    }

}