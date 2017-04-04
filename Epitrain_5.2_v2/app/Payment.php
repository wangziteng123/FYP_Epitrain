<?php

namespace App;
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;
use Stripe\Stripe;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Payment extends Authenticatable
{

    private $payGateway;
    private $card;





    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isAdmin() {
        return $this->isAdmin;
    }





     function setcard($value){
        echo "enter set card";
            try{
                $card = [
                    'number' => $value['card'],
                    'expiryMonth' => $value['expiremonth'],
                    'expiryYear' => $value['expireyear'],
                    'cvv' => $value['cvv']
                ];
                $ccard = new CreditCard($card);
                $ccard->validate();
                $this->card = $card;
                return true;
            }
            catch(\Exception $ex){
                return $ex->getMessage();
            }

     }

   function makepayment($value){
            try{

            // Set your secret key: remember to change this to your live secret key in production
            // See your keys here: https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey("sk_test_wZZaGd7Ztp3yQaOUuScbg6op");

            // Token is created using Stripe.js or Checkout!
            // Get the payment token submitted by the form:
            $token = $value['token'];
            $totalPrice= $value['amount'];
            $fidStr = $value['fidStr'];
            $userPaymentEmail = $value['receipt_email'];

            // Charge the user's card:
            $charge = \Stripe\Charge::create(array(
              "amount" => $totalPrice,
              "currency" => "sgd",
              "description" => $fidStr,
              "source" => $token,
              "receipt_email" => $userPaymentEmail
            ));


            $chargeID = $charge->id;
            $chargeOutcome = "Payment failed";
            $chargeOutcome = $charge->outcome->seller_message;


             if($chargeOutcome == "Payment complete."){
                return $chargeOutcome;

             }
             else{
                return $chargeOutcome;
             }



            }
            catch(\Exception $ex){
                return $ex->getMessage();
            }
        }















}
