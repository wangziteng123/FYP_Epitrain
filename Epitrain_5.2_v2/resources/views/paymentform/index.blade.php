@extends('layouts.app')

@section('content')


    <?php

    $totalPrice = $value["totalPrice"];
    $uid = $value["uid"];
    $fidStr = $value["fidStr"];

    $totalPrice = $totalPrice*100;


    ?>




<form action=<?php echo URL::route('paymentForm');?> method="POST">
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
  <input type="hidden" id="uid" name="uid" value=<?php echo $uid ?> />
  <input type="hidden" id="fidStr" name="fidStr" value=<?php echo $fidStr ?> />
</form>

















@endsection