@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Search Result</li>
        </ul>
    </div>
</div>
<?php
use App\Fileentry;

    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $parts = parse_url($url);
    parse_str($parts['query'], $query);

    $filenameArr = array(); 

     $imgSrc = $query['filename'];
     array_push($filenameArr,$imgSrc);

     $original_filename = $query['original_filename'];
     $id = $query['id'];
     $fileentry = Fileentry::find($id);
     $price = $fileentry -> price;
     $description = $fileentry -> description; 
     $category = $fileentry -> category; 
     $filename = $fileentry -> filename;

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


<div class="col-sm-12 col-xs-12" style="position:relative">

 <div class="col-sm-10 col-xs-10" style="position:relative">

    <!-- Blog Post -->

    <!-- Title -->
    <h1 style="position: absolute;left: 14px;">Search Results:</h1>
    <br/><br/><br/>
    <hr>

    <!--display search result-->
    <div class="jumbotron" style="background:#E1DFDE">
      <div class = "row">
          <div id="searchContainer" class="col-sm-1 col-sm-offset-1 hidden-xs"></div>
          <div class="col-sm-7 col-xs-8 col-xs-offset-1">
            <font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $original_filename;?></font>
          </div>
          <div class="col-sm-1 col-xs-1">
            <font color="black" style="font-size:28px">S$<?php echo $price?>
            </font>
          </div>
      </div>
      <div class = "row">
        <div class="col-sm-9 col-xs-9 col-xs-offset-1 col-sm-offset-1"><font color="black" size='4'><strong>Category:</strong>  <?php echo $category;?></font>
        </div>
        <div class="col-sm-offset-1 col-sm-9 hidden-xs">
          <font color="black" size='4'><strong>Description:</strong>  <?php 
            if (strlen($description) == 0) {
              echo "No description";
            } else {
              echo $description;
            } 
            ?></font>
        </div>
      </div>
      <div class = "row center-block">
        <div class="col-sm-3 col-xs-3 col-xs-offset-2">
          @if (count($shoppingcartExist))
              <button class="btn btn-warning" style="background-color: darkblue">
                  <font>Already Added</font>
              </button>
          @else
              <form action=<?php echo url('shoppingcart/add');?> method="post">
                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                  <input type="hidden" name="fid" value=<?php echo $id?>>
                      <button  class="btn btn-raised btn-info">
                          <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                          Add to Cart
                      </button>
               </form>
          @endif
        </div>
        <div class="col-sm-3 col-xs-3 col-xs-offset-2 hidden-xs">
            @if (count($libraryExist))
              <button class="btn btn-warning" style="background-color: darkblue">
                  <font style="">Bought Already</font>
              </button>
            @elseif (!Auth::user()->isAdmin)
            <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post">
                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                <input type="hidden" name="fidStr" value=<?php echo $id?>>
                    <button class="btn btn-raised btn-warning">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                        Buy Now
                    </button>
             </form>

            @endif
        </div>
      </div>
      <div class = "row center-block visible-xs">
        <div class="col-sm-3 col-xs-3 col-xs-offset-2">
          @if (count($libraryExist))
              <button class="btn btn-warning" style="background-color: darkblue">
                  <font style="">Already Purchased</font>
              </button>
            @elseif (!Auth::user()->isAdmin)
            <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post">
                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                <input type="hidden" name="fidStr" value=<?php echo $id?>>
                    <button class="btn btn-raised btn-warning">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                        Buy Now
                    </button>
             </form>

            @endif
        </div>
      </div>
    </div>
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
    // URL of PDF document
    var mainUrl = window.location.hostname;
    var url = "http://" + mainUrl + "/fileentry/get/" + filename_array[0];
    var divId = "searchContainer";
    getPreview(url, divId);            
               
</script> 

@endsection