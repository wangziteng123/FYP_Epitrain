<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactFormRequest;
use Mail;

class AboutController extends Controller
{
    //
		private $email = 'leanhtu188@gmail.com';
		
    public function create()
    {
        return view('about.contact');
    }

    public function store(ContactFormRequest $request)
    {
			
			$email_content = nl2br(e($request->get('message')));
			$message_content = $this->nl2br2($request->get('message'));
    	Mail::send('about.email',
        array(
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'user_message' => $message_content
        ), function($message)
	    {
	        $message->from('cavetzii@gmail.com','Epitrain Elearning Platform Robot');
	        $message->to($this->email, 'Admin')->subject('User Feedback');
	    });

    	return \Redirect::route('contact')
      	->with('message', 'Thanks for contacting us! The admin has been informed and will get back to you soon.');
    }
		
		public function nl2br2($string) { 
			$string2 = str_replace(array("\r\n", "\r", "\n"), ' \t\n ', $string); 
			return $string2; 
		} 
}
