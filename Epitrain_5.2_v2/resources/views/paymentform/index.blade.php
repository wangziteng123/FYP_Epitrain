@extends('layouts.app')

@section('content')


    <?php

    $totalPrice = $value["totalPrice"]; //retrieve the total price of the books being purchase
    $uid = $value["uid"];   //retrieve the user id
    $fidStr = $value["fidStr"]; //retrieve all the id of the books being purchase

    $totalPrice = $totalPrice*100;
    $totalPrice = round($totalPrice, 2);

     $fidStrArray = explode(",", $fidStr); // retrieve the fid of the books user want to purchase
     $sizeOfFidStrArray = count($fidStrArray);  //count how many books





        ?>




 <div class="container">
        <h1>Purchase list</h1>
        <table class="table table-bordered" style="background-color:white">
            <thead>
                <tr>

            <th   style="color:black;text-align:center">Book title</th>
            <th   style="color:black;text-align:center">Price</th>

                </tr>
            </thead>

           <tbody>



        <?php
         for($start = 0; $start < $sizeOfFidStrArray-1; $start++ ){

            $fileentry_id = $fidStrArray[$start +1];


            $shoppingcartExist = \DB::table('fileentries')
                            ->where('id', $fileentry_id)
                            ->get();

            if(count($shoppingcartExist)) {
                 //show it in the table, with its title
                  $title = $shoppingcartExist[0]->original_filename; // retrieve title of book
                  $price = $shoppingcartExist[0]->price; //retrieve price of books

             ?>

 
            <tr>
                <td style='color:black'  > <?php echo $title; ?> </td>
                <td style='color:black' > S$<?php echo  number_format($price, 2, '.', ''); ?>  </td>

            </tr>

<?php
            }

        }

    ?>


        </tbody>

            </table>

 		
			
<!-- Payment button from stripe -->
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