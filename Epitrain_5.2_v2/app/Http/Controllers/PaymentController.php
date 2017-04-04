<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;


use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Payment;

//use vendor\omnipay\stripe;
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;
//use Omnipay\Stripe;


class PaymentController extends Controller
{
    public function index(Request $request) {
            $totalPrice = $request->get('totalPrice');
            $fidStr = $request->get('fidStr');
            $uid =  $request->get('uid');

            $value = [
            "totalPrice"=>$totalPrice,
            "uid"=>$uid ,
            "fidStr" =>$fidStr
            ];
			
			
			
			if($totalPrice >0.5){
				 //return view('paymentform.index');
				return \View::make('paymentform.index')->with('value',$value);
			}else{
				$message = "Please let the admin know that you cannot purchase book of price less than 50 cents";
				return \View::make('about.contact')->with('errorMsg',$message);
				
			}

        
    }


  public function paymentForm(Request $request){
         // $category_id = $request->get('category');

          $pay = new Payment();

          $token = $request->get('stripeToken');



        \Stripe\Stripe::setApiKey("sk_test_wZZaGd7Ztp3yQaOUuScbg6op");
         $tokenJSON = \Stripe\Token::retrieve($token);
         $userPaymentEmail = $tokenJSON ->email;


          //$userPaymentEmail = $token['email'];
        //  echo "hello ";
         // echo $userPaymentEmail;
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


         if($response == "Payment complete."){
              app('App\Http\Controllers\ShoppingController')->addToLibrary($request); // call add to library method in shopping controller
             //return view('mylibrary.index');
             return view('shoppingcart.index');

         }
         else{

         return view('shoppingcart.index');
         }



      }





}
