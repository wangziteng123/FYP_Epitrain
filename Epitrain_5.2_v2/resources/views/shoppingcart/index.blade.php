@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Shopping Cart</li>
        </ul>
    </div>
</div>
<?php

		$shoppingcarts = \DB::table('shoppingcarts')
			->where('user_id', Auth::user()->id)
            ->join('fileentries', 'shoppingcarts.fileentry_id', '=', 'fileentries.id')
            ->select('shoppingcarts.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
            ->get();
        $totalprice = 0;
?>

<script>
function callApi(url) {
	$.post(url,function(data) {
		var mainUrl2 = window.location.hostname;
		window.location = "http://"+mainUrl2+"/mylibrary";
	});

}

</script>

<h1 style="position: absolute;left: 14px;">Shopping Cart</h1>
                <br/><br/><br/>
                <hr>

<div class="col-sm-12 col-xs-12" style="position:relative">
                <!-- Blog Post -->

                <!-- Title -->
         <?php
         	$countNum = 0;
            $filenameArr = array();
         ?>

        @foreach($shoppingcarts as $shoppingcart)
        <div class="col-sm-10 col-xs-10" style="position:relative">
        <?php

	        $totalprice += $shoppingcart->price;
	        $checkid = $shoppingcart->id;


	        $countNum ++;
            array_push($filenameArr,$shoppingcart->filename);
            $container = "container".$countNum;

        ?>
      	  <div class="jumbotron" style="background:#E1DFDE">
      	  	  <div class = "row">
      	  	      <form action=<?php echo url('shoppingcart/deleteShoppcart');?> method="post">
	      	  	  <div id=<?php echo $container?> class="col-md-1 hidden-xs hidden-sm"></div>
			  	  <div class="form-group">
			  	  	<div class="col-md-1">
			  	  		<div class="checkbox">
				  	  		<label>
					  	  		<input type="checkbox" id=<?php echo $checkid?> onclick="countTotalprice()" checked/>
					  	  	</label>
					  	 </div>
				  	</div>
			  	  </div>
				  <div class="col-md-6 col-sm-10 col-xs-10 col-xs-offset-1 text-xs-center"><font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $shoppingcart->original_filename;?></font></div>
			  	  
			  	  <div class="col-md-1 hidden-xs hidden-sm"><font color="black" style="font-size:28px">S$<?php echo $shoppingcart->price?></font>    </div>
			  	  <div class="col-md-1 hidden-xs hidden-sm">
				  	  	<input type="hidden" name="fid" value=<?php echo $shoppingcart->fileentry_id;?>>
			  	  		<input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
	                    <button type="submit" class="btn btn-raised btn-warning">
                        <font style="">Remove</font>
                    </button>
			  	  </form>
			  	  </div>
			  </div>
			  <div class = "row">
			  	  <div class="col-md-6 col-sm-9 col-xs-9 col-md-offset-3 col-xs-offset-1 col-sm-offset-1"><font color="black" size='4'><strong>Category:</strong>  <?php echo $shoppingcart->category;?></font></div>
				  <div class="col-sm-6 col-sm-offset-3 hidden-xs"><font color="black" size='4'><strong>Description:</strong>  <?php 
					if (strlen($shoppingcart->description) == 0) {
						echo "No description";
					} else {
						echo $shoppingcart->description;
					} 
				  ?></font></div> 	  	
	  	  	  </div>
	  	  	  <div class = "row center-block">
	  	  	  	  <div class="col-xs-12 col-sm-12 visible-xs visible-sm center-block"><font color="black" style="font-size:28px">S$<?php echo $shoppingcart->price?></font>    
	  	  	  	  </div>
			  	  <div class="col-xs-12 col-sm-12 visible-xs visible-sm center-block">
			  	  	<form action=<?php echo url('shoppingcart/deleteShoppcart');?> method="post">
			  	  	<input type="hidden" name="fid" value=<?php echo $shoppingcart->fileentry_id;?>>
			  	  		<input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
	                    <button type="submit" class="btn btn-raised btn-warning">
	                        <font style="">Remove</font>
	                    </button>
			  	  	</form>
			  	  </div>
	  	  	  </div>
	  	  </div>
	  
		</div>
	@endforeach
    
 </div>

 <?php
    use Carbon\Carbon;

    $isSubscribe = Auth::user()->subscribe;
 ?>


 <div class="col-md-3 col-xs-3">
 <table style="border:1px solid #aad122;">
 	<div style="position:absolute;left:40px;top:10px">
 		<font style="font-size:20px">Total:</font><br/>
 	</div>

 	 @if($isSubscribe)

 	 <?php
            $currentTime = Carbon::now();
            $userSubscribe = \DB::table('subscription')
              ->where('user_id', Auth::user()->id)
              ->first();

            $end_Date = Carbon::createFromFormat('Y-m-d H:i:s', $userSubscribe->end_date);
            $expireOrnot = $currentTime->lt($end_Date);
     ?>

     	@if($expireOrnot)
     		<div style="position:absolute;left:40px;top:35px">
	 		<font style="font-size:40px;color:darkblue">S$<span id="total-price"></span></font><br/>
	 		</div>
     	@else
     		<div style="position:absolute;left:40px;top:35px">
	 		<font style="font-size:40px;color:darkblue">S$<span id="total-price"></span></font><br/>
	 		</div>
     	@endif

 	 @else
 	 	<div style="position:absolute;left:40px;top:35px">
 		<font style="font-size:40px;color:darkblue">S$<span id="total-price"></span></font><br/>
 		</div>

 	 @endif
 	
 	<div style="position:absolute;left:40px;top:90px">
 		<button  class="btn btn-raised btn-primary initialism slide_open" style="width:200px;">
	  	  		Checkout
	  	</button>
 	</div>
 	
 </table>
 </div>
</div>



<!-- Slide in popup window-->

<div id="slide" class="well col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1" style="position:relative;top:30px">
	<button class="slide_close btn btn-default" style="position:absolute;right:20px"><i class="fa fa-times" aria-hidden="true"></i></button>
    
  	<form action="#">
	   <!--  <ul class="list-group final-checkout">
	     @foreach($shoppingcarts as $shoppingcart)
		  <li class="list-group-item" style="position:relative">&nbsp&nbsp<?php echo $shoppingcart->original_filename;?>
		  	&nbsp<span style="position:absolute;right:15px">S$<?php echo $shoppingcart->price;?></span></li>
		  @endforeach
		</ul> -->
		<div class="panel panel-default" style="position:relative;height:50px">
			<div class="panel-heading"><h4>Checkout</h4></div>
			<div class="panel-body">
				<font style="color:black;position:absolute;left:25px" size = "4"><span class="final-count"></span>&nbspebooks</font>
				 @if($isSubscribe)

			 	 <?php
			            $currentTime = Carbon::now();
			            $userSubscribe = \DB::table('subscription')
			              ->where('user_id', Auth::user()->id)
			              ->first();

			            $end_Date = Carbon::createFromFormat('Y-m-d H:i:s', $userSubscribe->end_date);
			            $expireOrnot = $currentTime->lt($end_Date);
			     ?>

			     	@if($expireOrnot)
			     		<font style="color:black; float:right" size = "4">Total: S$<span id="final-checkout">0</span></font>
			     	@else
			     		<font style="color:black; float:right" size = "4">Total: S$<span id="final-checkout"></span></font>
			     	@endif

			 	 @else
			 	 	<font style="color:black; float:right" size = "4">Total: S$<span id="final-checkout"></span></font>

			 	 @endif
			 	 <br/>
			 	 <br/>
			 	 <button class="btn btn-success btn-raised pull-right" style="display:inline-block" onclick="pay()"><i class="fa fa-credit-card-alt" aria-hidden="true"></i>&nbsp&nbspPay</button>
			</div>
		</div>
	</form>
    
</div>


<script>
$(document).ready(function () {

    $('#slide').popup({
        focusdelay: 400,
        outline: true,
        vertical: 'top'
    });

});
</script>


<script>
	var shoppingcarts = <?php echo json_encode($shoppingcarts)?>;
	var count = <?php echo count($shoppingcarts)?>;

	var totalprice = 0;
	var countFinal = 0;
	//document.querySelector('.total-price').innerHTML = totalprice;
	// alert("dsfsdf  " + document.getElementById("22").checked);
	for(i = 0; i < count; i++) {
		if(document.getElementById(shoppingcarts[i].id).checked) {
			totalprice += shoppingcarts[i].price;
			countFinal++;
		}
	}
	//alert("Info " + document.getElementById('total-price').innerHTML);

	document.getElementById('total-price').innerHTML = totalprice;
	document.getElementById('final-checkout').innerHTML = totalprice;
	document.querySelector('.final-count').innerHTML = countFinal;


	function countTotalprice() {
		var shoppingcarts = <?php echo json_encode($shoppingcarts)?>;
		
		var count = <?php echo count($shoppingcarts)?>;
		var totalprice = 0;
		var countFinal = 0;
		//document.querySelector('.total-price').innerHTML = totalprice;
		// alert("dsfsdf  " + document.getElementById("22").checked);

		for(i = 0; i < count; i++) {
			if(document.getElementById(shoppingcarts[i].id).checked) {
				totalprice += shoppingcarts[i].price;
				countFinal++;
			}
		}
		document.getElementById('total-price').innerHTML = totalprice;
		document.getElementById('final-checkout').innerHTML = totalprice;
		document.querySelector('.final-count').innerHTML = countFinal;
	}


	function pay() {
		var shoppingcarts = <?php echo json_encode($shoppingcarts)?>;
		var uid = <?php echo Auth::user()->id?>;
		var fidStr = "";
		var mainUrl = window.location.hostname;
		var countFinal = 0;

		for(i = 0; i < count; i++) {
			if(document.getElementById(shoppingcarts[i].id).checked) {
				countFinal++;
				fidStr = fidStr + "&fid" + countFinal + "=" + shoppingcarts[i].id;
			}
		}

		// //deploy
		// //mainUrl = "http://" + mainUrl + "/checkout?uid=" + uid + "&count=" + countFinal + fidStr;
		mainUrl = "http://" + mainUrl + "/shoppingcart/checkout?uid=" + uid + "&count=" + countFinal + fidStr;
		callApi(mainUrl); 
		// $.post(url,function(data) {
		// });

	}

</script>



<script>
    function getPreview(url, divId) {
        PDFJS.getDocument(url)
        .then(function(pdf) {
                    // Get div#container and cache it for later use
                    //var container = document.getElementById(divId);
                    var container = document.getElementById(divId);

                    // Loop from 1 to total_number_of_pages in PDF document
                    var i = 1;

                        // Get desired page
                        pdf.getPage(i).then(function(page) {

                          // Create a new Canvas element
                          var canvas = document.createElement("canvas");

                          var scale = 1.5;
                          var viewport = page.getViewport(canvas.width / page.getViewport(2.7).width);
                          var div = document.createElement("div");

                          // Set id attribute with page-#{pdf_page_number} format
                          div.setAttribute("id", "page-" + (page.pageIndex + 1));

                          // This will keep positions of child elements as per our needs
                          div.setAttribute("style", "position: relative");

                          // Append div within div#container
                          container.appendChild(div);

                          

                          // Append Canvas within div#page-#{pdf_page_number}
                          div.appendChild(canvas);

                          var context = canvas.getContext('2d');
                          canvas.height = viewport.height;  //1188
                          canvas.width = viewport.width;   //918
                          var renderContext = {
                            canvasContext: context,
                            viewport: viewport
                          };

                          // Render PDF page
                          page.render(renderContext)
                          .then(function() {
                            // Get text-fragments
                            return page.getTextContent();
                          })
                        });
                    
                      });



    }

</script>
   
 <script>
    <?php
        $js_array = json_encode($filenameArr);
        echo "var filename_array = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($shoppingcarts)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array[y - 1];
        var divId = "container" + y;
        getPreview(url, divId);
    }             
               
</script> 

@endsection