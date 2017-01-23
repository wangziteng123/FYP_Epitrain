@extends('layouts.app')

@section('content')
 


    <!-- Split button -->

   <!-- Page Content -->
    <div class="container">

        <!-- Jumbotron Header -->
        <!-- <header class="jumbotron hero-spacer" style="min-height: 300px;">
            <h1><font color="black">A Warm Welcome!</font></h1>
            <p><font style="color:black;font-weight:600" >Epitrain® is a provider of training, resource development and consultancy services. </font></p>
            <p>

                <div class="well" style="width:500px; margin:0 auto;">
                    <h4 style="color:black">Find the book you want</h4>
                    <div class="input-group">
                        <input type="text" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <span class="glyphicon glyphicon-search"></span>
                        </button>
                        </span>
                    </div>
                </div>
            </p>
        </header> -->

        <hr>

        <!-- Title -->
        <div class="row">
            <div class="col-lg-12">
                <h3>Suggestions</h3>
            </div>
        </div>
        <!-- /.row -->

        <!-- Page Features -->
        <div class="row text-center">
            <?php
                $countNum = 0;
                $filenameArr = array();
            ?>

            @foreach($entries as $entry)
                <?php
                     $countNum ++;
                     $price = $entry->price; 
                     $fid = $entry->id;
                     $filename = $entry->filename;
                     array_push($filenameArr,$filename);

                     $imgSrc = $entry->filename;
                     $pos = strpos($imgSrc, "pdf");
                     $imgSrc = substr($imgSrc, 0, $pos);
                     $imgSrc = "img/".$imgSrc."jpg";

                     //whether the book is in shoppingcart or not / library
                     $shoppingcartExist = \DB::table('shoppingcarts')
                        ->where('user_id', Auth::user()->id)
                        ->where('fileentry_id', $fid)
                        ->get();

                     $libraryExist = \DB::table('libraries')
                        ->where('user_id', Auth::user()->id)
                        ->where('fileentry_id', $fid)
                        ->get();

                    //$im = new imagick('../storage/app/public/php1BC0.tmp.pdf[0]');
                    // $im->setImageFormat('jpg');
                    // header('Content-Type: image/jpeg');
                    // echo $im;
                    
                   // $container = "container".$countNum;
                    $container = "container".$countNum;
                ?>
               

                <div class="col-md-3 col-sm-6 hero-feature">

                <div class="thumbnail" style="height:300px">
                    <!-- <div id="container"></div>
                        <img src=<?php echo $imgSrc?> width="100" height="100" alt="ALT NAME" class="img-responsive" /> 
                     -->
                    <div id=<?php echo $container?> style=""></div>
                     
                    <div class="caption" style="position:relative;height:150px">
                        <!-- <h3>{{$entry->original_filename}}</h3> -->
                        <?php $fileName = $entry->original_filename; ?>
                            @if(strlen($fileName) > 30)
                            <p>{{substr($fileName,0,30)."..." }}</p>
                            @else
                            <p>{{$fileName}}</p>
                            @endif
                        <p>Category: <?php echo $entry->category?></p>
                        <p style="position:absolute; left:30px"><font style="font-size:25px;color:#34495E">S$<?php echo $price?></font></p>

                        @if (count($libraryExist))
                            <form action=<?php echo url('#');?> method="post" style="position:absolute;right:120px;top:70px;border:none">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid?>>
                                <button type="submit" style="border:none;background-color: Transparent">
                                   <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Bought already."></i>
                                </button>
                            </form>
                            <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:118px;top:84px"></i>
                        @else
                            <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post" style="position:absolute;right:120px;top:70px;border:none">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid?>>
                                <button type="submit" style="border:none;background-color: Transparent">
                                   <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                </button>
                            </form>
                        @endif


                        @if (count($shoppingcartExist))
                            <form action=<?php echo url('#');?> method="post" style="position:absolute;right:121px;top:94px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid?>>
                                <button type="submit" style="border:none;background-color: Transparent">
                                   <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Added already."></i>
                                </button>
                            </form>
                            <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:120px;top:106px"></i>
                        @else
                             <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:121px;top:94px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid?>>
                                <button type="submit" style="border:none;background-color: Transparent">
                                   <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                </button>
                            </form>                
                        @endif
                        
                               <!-- <a href="#" style="position:absolute;right:120px;top:95px">
                                <span id="shoppingcartSpan" onclick="addToShoppingCart()" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                        </a> -->

                        <!-- <a href=<?php echo url('shoppingcart/add?uid='+Auth::user()->id+'&fid='+$fid) ?> style="position:absolute;right:120px;top:70px">
                                <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                        </a> -->
                        <p style="position:absolute;right:20px;top:77px">
                            <button  class="btn btn-three" style="height:38px">
                                More Info
                            </button>
                        </p>
                    </div>
                </div>

                </div>
            @endforeach

        </div>
        <!-- /.row -->

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Your Website 2014</p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->

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
    //var url = "http://mozilla.github.io/pdf.js/web/compressed.tracemonkey-pldi-09.pdf";
    //var url = "http://" + mainUrl + ":8000/fileentry/get/" + <?php echo $filename;?>;
    //var url = "http://localhost:8000/fileentry/get/php1BC0.tmp.pdf";           
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array[y - 1];
        var divId = "container" + y;
        getPreview(url, divId);
    }             
               
</script> 


@endsection


