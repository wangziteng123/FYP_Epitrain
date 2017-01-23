<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Log;

class SessionTimeout {

    protected $session;
    protected $timeout = 900;

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
        $isLoggedIn = $request->path() != 'login';
        //error_log($request->path());
        //console.log($request->path());
        Log::info($request->path());

        if(!$request->session()->has('lastActivityTime')) {
            $request->session()->put('lastActivityTime', time());
            //Log::alert("Case A");
            //Log::notice($request->session()->get('lastActivityTime'));
        } elseif (time() - $request->session()->get('lastActivityTime') > $this->timeout){
            //Log::alert("Case B");
            //Log::notice($request->session()->get('lastActivityTime'));
            $request->session()->forget('lastActivityTime');
            //$cookie = cookie('intend', $isLoggedIn ? url()->current() : 'dashboard');
            //$email = $request->user()->email;
            auth()->logout();
            flash('You were inactive for '.$this->timeout/60 .' minutes. This platform will automatically log you out after 15 minutes.', 'danger');
            //Log::debug(session()->has('flash_notification.message'));
            return redirect('home');
            //return message('You had not activity in '.$this->timeout/60 .' minutes ago.', 'warning', 'login')->withInput(compact('email'))->withCookie($cookie);
        }
        $isLoggedIn ? $request->session()->put('lastActivityTime', time()) : $request->session()->forget('lastActivityTime');
        if (session()->has('flash_notification.message') == 1) {
            flash('You were inactive for '.ceil($this->timeout/60) .' minutes. This platform will automatically log you out after 15 minutes.', 'danger');
        }
        //Log::warning(session()->has('flash_notification.message'). " here");
        return $next($request);
    }

}