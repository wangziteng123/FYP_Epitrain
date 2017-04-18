<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Payment;

//use vendor\omnipay\stripe;
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;
//use Omnipay\Stripe;


class PaymentController extends Controller
{
    /**
    *check to ensure that user has select at least one book to purchase.
    *
    * @return array $value
    */
    public function index(Request $request) {
            $totalPrice = $request->get('totalPrice');
            $fidStr = $request->get('fidStr');
            $uid =  $request->get('uid');

            $value = [
            "totalPrice"=>$totalPrice,
            "uid"=>$uid ,
            "fidStr" =>$fidStr
            ];


			// only allow payment for price larger than 50 cents
			if($totalPrice >0.5){
				return \View::make('paymentform.index')->with('value',$value);
			}
			else if($totalPrice = null || strlen($fidStr) >1 ){
			    $message = "Please let the admin know that you cannot purchase book of price less than 50 cents";
                return redirect('shoppingcart')->with('message',$message);
			}else{
				  $message = "Please select at least a book";

				return redirect('shoppingcart')->with('errorMsg',$message);


			}


    }
    /**
    *receive the payment details, such as books and user id, from the purchase list.
    *Invoke makePayment() method in the payment class for payment and invoke addToLibrary() to add books into library after successful payment
    *
    *@param Request $request
    *
    * @return void
    */
    public function paymentForm(Request $request){

          $pay = new Payment();
          $token = $request->get('stripeToken');


        \Stripe\Stripe::setApiKey("sk_test_wZZaGd7Ztp3yQaOUuScbg6op");
         $tokenJSON = \Stripe\Token::retrieve($token);
         $userPaymentEmail = $tokenJSON ->email;

          $totalPrice = $request->get('amount');
          $uid = $request->get('uid');
          $fidStr = $request->get('fidStr');

           $valueForPayment = [

                  "amount" =>$totalPrice,
                  "currency"=> "SGD",
                  "token" => $token,
                  "uid" => $uid,
                  "fidStr" => $fidStr,  // contains the bought book fid
                  "receipt_email" => $userPaymentEmail

           ];

         $response= $pay->makepayment($valueForPayment);  // calling a method in payment class
         $chargeID = $response["chargeID"];
            $chargeOutcome = $response["chargeOutcome"];

         if($chargeOutcome == "Payment complete."){
             app('App\Http\Controllers\ShoppingController')->addToLibrary($request); // call add to library method in shopping controller
            DB::insert('insert into transactionhistory (user_id, chargeID) values (?, ?)', [$uid,$chargeID ]);
             //return view('mylibrary.index');
             return view('shoppingcart.index');

         }
         else{

         return view('shoppingcart.index');
         }



    }
    /**
    *retrieve all the transactions made by a user from stripe
    *
    * @return array $allChargeDetails
    */
    public function viewTransaction(){
            $user_id = Auth::user()->id;
            $userCharges = DB::table('transactionhistory')
            ->where('user_id', '=', $user_id)
            ->get();


             $sizeOfUserChargesArray = count($userCharges);
                $chargeIDArray = array();
                $allChargeDetails = array(); // this array to store all charges details-> amount & description
                $oneChargeDetails = array(); // this array to store one charges details-> amount & description of one particular charge
            //retrieve charges from stripe
            \Stripe\Stripe::setApiKey("sk_test_wZZaGd7Ztp3yQaOUuScbg6op");


             for($start=0; $start < $sizeOfUserChargesArray; $start++){

                echo "hello";
                $chargeIDArray[$start]= $userCharges[$start]->chargeID;
                $charge= \Stripe\Charge::retrieve($chargeIDArray[$start]);

                $oneChargeDetails[0] = $charge->description;
                $oneChargeDetails[1] = $charge->amount;
                $oneChargeDetails[2] = $charge->receipt_email;

                $allChargeDetails[$start] = $oneChargeDetails;

             }


            return \View::make('transactionhistory.index')->with('allChargeDetails',$allChargeDetails);
    }
	  

    /**
    *allow payment For Subscription
    *
    *@param Request $request
    * @return array $value
    */
	public function paymentForSubscription(Request $request){

        $totalPrice = $request->get('amount');
        $uid =  $request->get('uid');
        $period = $request->get('period');

        $value = [
            "totalPrice"=>$totalPrice,
            "uid"=>$uid ,
            "period"=>$period
        ];


        return \View::make('subscriptionplan.index')->with('value',$value);


    }
    /**
    *receive the payment details for subscriptipn, such as the amount and period of subscription
    *
    *@param Request $request
    *
    * @return void
    */
    public function subscriptionPaymentForm(Request $request){

        $pay = new Payment();

		$token = $request->get('stripeToken');



        \Stripe\Stripe::setApiKey("sk_test_wZZaGd7Ztp3yQaOUuScbg6op");
        $tokenJSON = \Stripe\Token::retrieve($token);
        $userPaymentEmail = $tokenJSON ->email;



		$totalPrice = $request->get('amount');
        $uid = $request->get('uid');
        $period=$request->get('period');


        $valueForPayment = [

            "amount" =>$totalPrice,
            "currency"=> "SGD",
            "token" => $token,
            "uid" => $uid,
            "period" => $period,
            "receipt_email" => $userPaymentEmail

        ];

               $response= $pay->makepaymentSubscription($valueForPayment);  // calling a method in payment class
               $chargeID = $response["chargeID"];
                  $chargeOutcome = $response["chargeOutcome"];

        if($chargeOutcome == "Payment complete."){
            app('App\Http\Controllers\SubscriptionController')->addSubscription($request); // call add to subscription method in subscription controller

			return redirect('home');


        }
        else{
		    return redirect('home');
        }


    }


}
