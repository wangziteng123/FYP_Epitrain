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
    public function create()
    {
        return view('about.contact');
    }

    public function store(ContactFormRequest $request)
    {
    	Mail::send('about.email',
        array(
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'user_message' => $request->get('message')
        ), function($message)
	    {
	        $message->from('cavetzii@gmail.com','Epitrain Elearning Platform Robot');
	        $message->to('hoanvu.ngo.2014@smu.edu.sg', 'Admin')->subject('User Feedback');
	    });

    	return \Redirect::route('contact')
      	->with('message', 'Thanks for contacting us! The admin has been informed and will get back to you soon.');
    }
}
