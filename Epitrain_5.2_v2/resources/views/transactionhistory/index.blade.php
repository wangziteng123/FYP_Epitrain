@extends('layouts.app')

@section('content')


    <?php

        $sizeOfChargeArray = count($allChargeDetails);


        ?>



 <div class="container">
        <h1>Transaction History </h1>
        <table class="table table-bordered" style="background-color:white">
            <thead>
                <tr>

            <th   style="color:black;text-align:center">Book Purchased per transaction</th>
            <th   style="color:black;text-align:center">Email for receipt</th>
            <th   style="color:black;text-align:center">Total Amount</th>

                </tr>
            </thead>

           <tbody>



        <?php
         for($start = 0; $start < $sizeOfChargeArray; $start++ ){
                 $title="";

            $fidDescription = $allChargeDetails[$start][0]; // getting the description
            $totalAmount = $allChargeDetails[$start][1]/100;
            $emailReceipt = $allChargeDetails[$start][2];

            $fidStrArray= explode(",", $fidDescription);
            $sizeOfFidStrArray = count($fidStrArray);
            for($startForFid = 0; $startForFid< $sizeOfFidStrArray-1; $startForFid++){
            $fileentry_id = $fidStrArray[$startForFid +1];


            $shoppingcartExist = \DB::table('fileentries')
                            ->where('id', $fileentry_id)
                            ->get();


                if(count($shoppingcartExist)) {
                     //show it in the table, with its title

                     if(strlen($title)>0){
                      $title = $title." & ". $shoppingcartExist[0]->original_filename; // retrieve title of book
                     }
                     else{
                      $title = $shoppingcartExist[0]->original_filename; // retrieve title of book
                     }


             ?>



<?php
                }
            }

            ?>


     <tr>
                 <td style='color:black'  > <?php echo $title; ?> </td>
                 <td style='color:black' >  <?php echo $emailReceipt; ?>  </td>
                 <td style='color:black' > S$<?php echo $totalAmount; ?>  </td>

             </tr>


            <?php

        }

    ?>


        </tbody>

            </table>










@endsection