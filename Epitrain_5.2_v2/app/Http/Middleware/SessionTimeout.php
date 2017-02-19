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
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //$isLoggedIn = $request->path() != 'login';
        //Log::info($request->path());
        if (Auth::check()) {
            $user_record = DB::table('sessions')->where('user_id', auth()->user()->id)->first();
            if (time() - $user_record->last_activity > 1800) {
                auth()->logout();
                return redirect('login')->with('danger', 'You were inactive for more than 30 minutes. This platform will automatically log you out after 30 minutes.');
            } else {
                DB::table('sessions')->where('user_id', auth()->user()->id)
                ->update(
                    ['last_activity' => time()]
                );
            }
        }

        /*if(!$request->session()->has('lastActivityTime')) {
            $request->session()->put('lastActivityTime', time());
        } elseif (time() - $request->session()->get('lastActivityTime') > $this->timeout){
            $request->session()->forget('lastActivityTime');
            auth()->logout();

            flash('You were inactive for more than 30 minutes. This platform will automatically log you out after 30 minutes.', 'danger');
            return redirect('home');
        } elseif (Auth::check()) {
            $request->session()->put('lastActivityTime', time());
        } else {
            $request->session()->forget('lastActivityTime');
        }
        //$isLoggedIn ? $request->session()->put('lastActivityTime', time()) : $request->session()->forget('lastActivityTime');
        if (strcmp(session()->get('flash_notification.message'),'You were inactive for more than 30 minutes. This platform will automatically log you out after 30 minutes.') == 0) {
            flash('You were inactive for more than 30 minutes. This platform will automatically log you out after 30 minutes.', 'danger');
        }*/
        //Log::warning(session()->has('flash_notification.message'). " here");
        return $next($request);
    }

}