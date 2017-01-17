@extends('layouts.app')

@section('content')
<?php

		$shoppingcarts = \DB::table('shoppingcarts')
			->where('user_id', Auth::user()->id)
            ->join('fileentries', 'shoppingcarts.fileentry_id', '=', 'fileentries.id')
            ->select('shoppingcarts.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
            ->get();
        $totalprice = 0;
?>

<div class="col-lg-12" style="position:relative">

 <div class="col-lg-8" style="position:relative; left:90px">

                <!-- Blog Post -->

                <!-- Title -->
                <h1 style="position: absolute;left: 14px;">Shopping Cart</h1>
                <br/><br/><br/>
                <hr>

        @foreach($shoppingcarts as $shoppingcart)
        <?php
        	$imgSrc = $shoppingcart->filename;
	        $pos = strpos($imgSrc, "pdf");
	        $imgSrc = substr($imgSrc, 0, $pos);
	        $imgSrc = "img/".$imgSrc."jpg";

	        $totalprice += $shoppingcart->price;

        ?>
      	  <div class="jumbotron" style="position:relative;height:180px;background:#E1DFDE">
			  <img src=<?php echo $imgSrc?> alt="Smiley face" height="165" width="102" style="position:absolute;left:5px;top:7px;">
				  <div  style="position:absolute;left:130px;top:10px;"><font color="#aad122" style="font-size: 25px;font-weight: bold;"><?php echo $shoppingcart->original_filename;?></font></div>
				  <div  style="position:absolute;left:132px;top:43px;"><font color="black">category: <?php echo $shoppingcart->category;?></font></div>
				  <div  style="position:absolute;left:130px;top:71px;"><font color="black"><?php echo $shoppingcart->description;?></font></div>
			  
			  	  <div style="position:absolute;right:170px;top:15px;"><font color="black" style="font-size:20px">S$<?php echo $shoppingcart->price?></font></div>
			  	  <div style="position:absolute;right:105px;top:20px;">
			  	  	<form action=<?php echo url('shoppingcart/deleteShoppcart');?> method="post">
			  	  		<input type="hidden" name="fid" value=<?php echo $shoppingcart->fileentry_id?>>
                        <button type="submit" style="border:none;background-color: Transparent">
                            <font style="color:#3079C1">Remove</font>
                        </button>
			  	  	</form>
			  	  	
	  	  </div>
	  
		</div>
	@endforeach
    
 </div>

 <div class="col-lg-3" style="position:relative;left:90px;top:72px;">
 <table style="border:1px solid #aad122;">
 	<div style="position:absolute;left:40px;top:10px">
 		<font style="font-size:20px">Total:</font><br/>
 	</div>

 	<div style="position:absolute;left:40px;top:35px">
 		<font style="font-size:40px;color:#aad122">S$<?php echo $totalprice?></font><br/>
 	</div>

 	<div style="position:absolute;left:40px;top:90px">
 		<button  class="btn btn-four" style="width:200px;">
	  	  		Checkout
	  	</button>
 	</div>
 </table>
 </div>
</div>










@endsection