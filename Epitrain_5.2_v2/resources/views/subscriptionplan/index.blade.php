@extends('layouts.app')

@section('content')

    <?php

    $totalPrice = $value["totalPrice"]; //retrieve the total price of the books being purchase
    $uid = $value["uid"];   //retrieve the user id
    $period = $value["period"];


        ?>




 <div class="container">
        <h1>Subscription Payment</h1></br>
 </div>
 <div class="container">
        <p>You have selected a subscription plan of <b><?php echo $period; ?> days</b> for <b>$<?php echo $totalPrice ?>.</b> </br>
        This will allow you free access to all materials for the next <?php echo $period; ?> days
        after which your plan will expires. </p></br>


 </div>
<?php    $totalPrice = $totalPrice*100; ?>
 		
			
<!-- Payment button from stripe -->
<form action=<?php echo URL::route('subscribePaymentForm');?> method="POST">
  <script id="stripe-data"
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="pk_test_yCdx9f05Iu5CIqwZLE8q4NCu"
    data-amount=<?php echo $totalPrice ?>
    data-name="Stripe.com"
    data-description="Widget"
    data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
    data-locale="auto"
    data-zip-code="true"
    data-currency="sgd">
  </script>
  <input type="hidden" id="amount" name="amount" value=<?php echo $totalPrice ?> />
  <input type="hidden" id="period" name="period" value=<?php echo $period ?>/>
  <input type="hidden" id="uid" name="uid" value=<?php echo $uid ?> />
</form>

  
 	


@endsection