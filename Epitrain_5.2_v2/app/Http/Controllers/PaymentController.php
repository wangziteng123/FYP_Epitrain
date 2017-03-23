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

         //return view('paymentform.index');
         return \View::make('paymentform.index')->with('value',$value);
    }


    public function paymentForm(Request $request){
       // $category_id = $request->get('category');

        $pay = new Payment();

        $token = $request->get('stripeToken');
        $totalPrice = $request->get('amount');
        $uid = $request->get('uid');

        $fidStr = $request->get('fidStr');


         $valueForPayment = [

                "amount" =>$totalPrice,  // hardcode an amt
                "currency"=> "SGD",
                "token" => $token


         ];

       $response= $pay->makepayment($valueForPayment);


       if($response == "Payment complete."){
            app('App\Http\Controllers\ShoppingController')->addToLibrary($request);
           //return view('mylibrary.index');
           return view('shoppingcart.index');

       }
       else{

       return view('shoppingcart.index');
       }



    }






}
