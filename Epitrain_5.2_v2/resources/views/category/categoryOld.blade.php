@extends('layouts.app')

@section('content')


<div class="col-lg-12" style="position:relative">

 <div class="col-lg-8" style="position:relative; left:90px">

          <!-- Blog Post -->

          <!-- Title -->
          <h1 style="position: absolute;left: 14px;">Category: <?php echo $category?></h1>
          <br/><br/><br/>
          <hr>

         <?php

         	$countNum = 0;
            $filenameArr = array();
         ?>

        @foreach($entries as $entry)
        <?php
	        $countNum ++;
            array_push($filenameArr,$entry->filename);
            $container = "container".$countNum;

             $original_filename = $entry->original_filename;
		     $price = $entry->price;
		     $description = $entry->description; 
		     $category = $entry->category; 
		     $filename = $entry->filename;
		     $id = $entry->id;

		     //whether the book is in shoppingcart or not / library
		     $shoppingcartExist = \DB::table('shoppingcarts')
		        ->where('user_id', Auth::user()->id)
		        ->where('fileentry_id', $id)
		        ->get();

		     $libraryExist = \DB::table('libraries')
		        ->where('user_id', Auth::user()->id)
		        ->where('fileentry_id', $id)
		        ->get();



        ?>
      	     <!--display search result-->
    <div class="jumbotron" style="position:relative;height:180px;background:#E1DFDE">
          <div id=<?php echo $container?> style="position:absolute;left:5px;top:7px;"></div>
		  <div  style="position:absolute;left:130px;top:10px;"><font color="#aad122" style="font-size: 25px;font-weight: bold;"><?php echo $original_filename;?></font></div>
		  <div  style="position:absolute;left:132px;top:43px;"><font color="black">category: <?php echo $category?></font></div>
		  <div  style="position:absolute;left:130px;top:71px;"><font color="black"><?php echo $description?></font></div>
	  
	  	  <div style="position:absolute;right:170px;top:15px;"><font color="black" style="font-size:20px">S$<?php echo $price ?></font></div>
	  	  
          <div style="position:absolute;right:45px;top:15px;">

            @if (count($libraryExist))
                <button  class="btn btn-three" style="width:115px;border-color:#ABB2B9">
                        <font style="color:#ABB2B9">Bought Already</font>
                </button>
            @else
            <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post">
                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                <input type="hidden" name="fid" value=<?php echo $id?>>
                    <button  class="btn btn-three" style="width:115px">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                        Buy Now
                    </button>
             </form>

            @endif
	  	  	
	  	  	
	  	  </div>

          <div style="position:absolute;right:45px;top:53px;">

            @if (count($shoppingcartExist))
                <button  class="btn btn-three" style="width:115px;border-color:#ABB2B9" >
                        <font style="color:#ABB2B9">Added Already</font>
                </button>
            @else
                <form action=<?php echo url('shoppingcart/add');?> method="post">
                    <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                    <input type="hidden" name="fid" value=<?php echo $id?>>
                        <button  class="btn btn-three">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            Add to Cart
                        </button>
                 </form>
            @endif
          </div>
	  
	</div>
	@endforeach

	{{ $entries->appends(['category' => $category])->render()}}
    
 </div>
</div>





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
    var countEntries = <?php echo count($entries)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + ":8000/fileentry/get/" + filename_array[y - 1];
        var divId = "container" + y;
        getPreview(url, divId);
    }             
               
</script> 

@endsection