@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">My Library</li>
        </ul>
    </div>
</div>
<?php
        $entries2 = \DB::table('libraries')
            ->where('user_id', Auth::user()->id)
            ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
            ->select('libraries.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
            ->get();

?>
<div class="col-lg-12" style="position:relative">

 <div class="col-lg-10" style="position:relative; left:90px">

                <!-- Blog Post -->

                <!-- Title -->
                <h1 style="position: absolute;left: 14px;">My Library</h1>
                <br/><br/><br/>
                <hr>
            <?php
                $countNum = 0;
                $filenameArr = array();
            ?>

            @foreach($entries2 as $entry)
                   <?php
                        $countNum ++;
                        array_push($filenameArr,$entry->filename);
                        $container = "container".$countNum;
                    ?>

                <div class="col-sm-4 col-xs-5 col-md-3 hero-feature" >

                <div class="thumbnail" style="height:300px">
                    <div id=<?php echo $container?> style=""></div>

                    <div class="caption">
                       <!--  <h3>{{$entry->original_filename}}</h3> -->

                        <?php 
                          $fileName = $entry->original_filename; 
                          $fileCat = $entry->category;
                          $fileDescription = $entry->description;
                        ?>
                    @if(strlen($fileName) > 30)
                      <p>{{substr($fileName,0,30)."..." }}</p>
                    @else
                      <p>{{$fileName}}</p>
                    @endif

                    <p><strong>Category:  </strong>{{$fileCat}}<br/></p>
                    
                    @if(strlen($fileDescription) > 0)
                      <p><strong>Description:</strong><br/>{{$fileDescription}}</p>
                    @else
                      <p>No description is available for this ebook.</p>
                    @endif
                    <p>
                        <a href="{{route('getviewer', $entry->filename)}}" class="btn-raised btn-info btn">View</a> 
                    </p>
                    </div>
                </div>

                </div>
            @endforeach

        

    
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
    var countEntries = <?php echo count($entries2)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array[y - 1];
        var divId = "container" + y;
        getPreview(url, divId);
    }             
               
</script> 


@endsection