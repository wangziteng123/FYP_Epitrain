<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactFormRequest;
use Mail;

/**
 * AboutController Class used for Contact Admin function
 */
class AboutController extends Controller
{
    //
		private $email;
		
	/**
	* create
	*
	* generate the page of Contact Admin
	*
	* @return view of the page
	*/
    public function create()
    {
        return view('about.contact');
    }
    
	/**
	 * getEmail
	 *
	 * get the email adress of the admin user, to send userfeedback to admin as an email
	 *
	 * @return void
	 */
    public function getEmail(){
        $emailAdd = \DB::table('adminemail')
            -> orderBy('email_id', 'DESC')
            -> first();
        
        $emailAddress = $emailAdd->email;
        $this->email = $emailAddress; 
    }

	/**
	 * store function receives emails sent from users using Contact Admin function and stores it as ‘User Feedback’. 
	 * It will return a message which displays on user screen which is ‘Thanks for contacting us! The admin has been informed and will get back to you soon.’
	 *
	 * @param ContactFormRequest $request which is the request to contact admin
	 * @return view of the success or error message
	 */
    public function store(ContactFormRequest $request)
    {
			$email = $this->getEmail();
            
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
