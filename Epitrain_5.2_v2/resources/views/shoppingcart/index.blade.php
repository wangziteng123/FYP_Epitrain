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

<script>
function callApi(url) {
	$.post(url,function(data) {
		var mainUrl2 = window.location.hostname;
		window.location = "http://"+mainUrl2+"/mylibrary";
	});

}

</script>


<div class="col-lg-12" style="position:relative">

 <div class="col-lg-8" style="position:relative; left:90px">

                <!-- Blog Post -->

                <!-- Title -->
                <h1 style="position: absolute;left: 14px;">Shopping Cart</h1>
                <br/><br/><br/>
                <hr>

         <?php
         	$countNum = 0;
            $filenameArr = array();
         ?>

        @foreach($shoppingcarts as $shoppingcart)
        <?php

	        $totalprice += $shoppingcart->price;
	        $checkid = $shoppingcart->id;


	        $countNum ++;
            array_push($filenameArr,$shoppingcart->filename);
            $container = "container".$countNum;

        ?>
      	  <div class="jumbotron" style="position:relative;height:180px;background:#E1DFDE">
      	  	  <div id=<?php echo $container?> style="position:absolute;left:5px;top:7px;"></div>
			  	  <div  style="position:absolute;left:120px;top:18px;"><input type="checkbox" id=<?php echo $checkid?> style="width:16px;height:16px" onclick="countTotalprice()" checked></div>
				  <div  style="position:absolute;left:140px;top:10px;"><font color="#aad122" style="font-size: 25px;font-weight: bold;"><?php echo $shoppingcart->original_filename;?></font></div>
				  <div  style="position:absolute;left:132px;top:43px;"><font color="black">category: <?php echo $shoppingcart->category;?></font></div>
				  <div  style="position:absolute;left:130px;top:71px;"><font color="black"><?php echo $shoppingcart->description;?></font></div>
			  
			  	  <div style="position:absolute;right:170px;top:15px;"><font color="black" style="font-size:20px">S$<?php echo $shoppingcart->price?></font></div>
			  	  <div style="position:absolute;right:105px;top:20px;">
			  	  	<form action=<?php echo url('shoppingcart/deleteShoppcart');?> method="post">
			  	  		<input type="hidden" name="fid" value=<?php echo $shoppingcart->fileentry_id;?>>
			  	  		<input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
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

 	<div style="position:absolute;left:40px;top:35px" id="total-price">
 		<font style="font-size:40px;color:#aad122">S$<span class="total-price"></span></font><br/>
 	</div>

 	<div style="position:absolute;left:40px;top:90px">
 		<button  class="btn btn-four initialism slide_open" style="width:200px;">
	  	  		Checkout
	  	</button>
 	</div>
 	
 </table>
 </div>
</div>



<!-- Slide in popup window-->

<div id="slide" class="well" style="position:relative;top:30px;width:600px;height:400px">
	<button class="slide_close btn btn-default" style="position:absolute;right:20px"><i class="fa fa-times" aria-hidden="true"></i></button>
    <h4>Checkout</h4>
  	<form action="#">
	   <!--  <ul class="list-group final-checkout">
	     @foreach($shoppingcarts as $shoppingcart)
		  <li class="list-group-item" style="position:relative">&nbsp&nbsp<?php echo $shoppingcart->original_filename;?>
		  	&nbsp<span style="position:absolute;right:15px">S$<?php echo $shoppingcart->price;?></span></li>
		  @endforeach
		</ul> -->
		<div class="panel panel-default" style="position:relative;height:50px">
			<div class="panel-body">
				<font style="color:black;position:absolute;left:25px"><span class="final-count"></span>&nbsp ebooks</font>
			    <font style="color:black;position:absolute;right:35px">Total: S$<span class="final-checkout"></span></font>
			</div>
		</div>
	</form>
    <button class="btn btn-default" style="position:absolute;right:30px;width:70px" onclick="pay()"><i class="fa fa-credit-card-alt" aria-hidden="true"></i>&nbsp&nbspPay</button>
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
	//alert("dsfs " + totalprice);
	document.querySelector('.total-price').innerHTML = totalprice;
	document.querySelector('.final-checkout').innerHTML = totalprice;
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
		//alert("dsfs " + totalprice);
		document.querySelector('.total-price').innerHTML = totalprice;
		document.querySelector('.final-checkout').innerHTML = totalprice;
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