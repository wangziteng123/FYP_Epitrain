@extends('layouts.app')

@section('content')

<?php
use App\Fileentry;

	$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$parts = parse_url($url);
	parse_str($parts['query'], $query);

	 $imgSrc = $query['filename'];
     $pos = strpos($imgSrc, "pdf");
     $imgSrc = substr($imgSrc, 0, $pos);
     $imgSrc = "img/".$imgSrc."jpg";

    // echo $query['filename'];
     $original_filename = $query['original_filename'];
     $id = $query['id'];
     $fileentry = Fileentry::find($id);
     $price = $fileentry -> price;
     $description = $fileentry -> description; 
     $category = $fileentry -> category; 
?>


<div class="col-lg-12" style="position:relative">

 <div class="col-lg-8" style="position:relative; left:90px">

                <!-- Blog Post -->

                <!-- Title -->
                <h1 style="position: absolute;left: 14px;">Search Results:</h1>
                <br/><br/><br/>
                <hr>

    <!--display search result-->
    <div class="jumbotron" style="position:relative;height:180px;background:#E1DFDE">
	  <img src=<?php echo $imgSrc;?> alt="Smiley face" height="165" width="102" style="position:absolute;left:5px;top:7px;">
		  <div  style="position:absolute;left:130px;top:10px;"><font color="#aad122" style="font-size: 25px;font-weight: bold;"><?php echo $original_filename;?></font></div><>
		  <div  style="position:absolute;left:132px;top:43px;"><font color="black">category: <?php echo $category?></font></div>
		  <div  style="position:absolute;left:130px;top:71px;"><font color="black"><?php echo $description?></font></div>
	  
	  	  <div style="position:absolute;right:170px;top:15px;"><font color="black" style="font-size:20px">S$<?php echo $price ?></font></div>
	  	  <div style="position:absolute;right:45px;top:15px;">
	  	  	
	  	  	<form action=<?php echo url('shoppingcart/add');?> method="post">
                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                <input type="hidden" name="fid" value=<?php echo $id?>>
                    <button  class="btn btn-three">
	  	  		<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
	  	  		Add to Cart
	  	  	</button>
             </form>
	  	  </div>
	  
	</div>
 </div>
</div>


@endsection