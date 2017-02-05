@extends('layouts.app')

@section('content')

@if (session()->has('flash_notification.message'))
       <div class="alert alert-{{ session('flash_notification.level') }}">
           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

           {!! session('flash_notification.message') !!}
       </div>
@endif


<h1><font color='white'>Manage Library</h1>

    <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer" style="position:relative;width:500px; margin:0 auto; min-height: 480px; border-radius: 10px;">
            <h3><font color="black">Upload New File</font></h3>
            <form action="fileentry/add" id="uploadform" method="post" enctype="multipart/form-data" style="width: 400px; margin:0 auto;border: 0px solid white;" onsubmit="">
                <div class="well" style="width:400px; margin:0 auto;">
                    <h4 style="color:black; float: left">Choose the file to upload</h4>
                    <div class="input-group">
                        <input type="file" name="filefield" value="{{ csrf_token() }}" style="color:black" required>
                    </div>
                    <!-- /.input-group -->
                </div>
                  <br/>

                <div style="position:absolute;left:55px">
                  <font style="font-weight:bold;color:black">Choose Category:</font>
                  <select name="category" style="color:black">
                    <option value="Trading" selected="selected">Trading</option>
                    <option value="RiskManagement">Risk Management</option>
                    <option value="Fintech">Fintech</option>
                    <option value="ProjectManagement">Project Management</option>
                    <option value="Fiance">Finance</option>
                    <option value="BusinessManagement">Business Management</option>
                    <option value="Leadership">Leadership</option>
                    <option value="FincialMarkets">Financial markets</option>
                  </select>

                </div>
                 <br/><br/>
                <div style="position:absolute;left:55px">
                  <font style="font-weight:bold;color:black">Add Price:&nbsp</font> <input type="number" name="price" step="any" style="color:black">
                </div>
                <br/><br/>
                 
                 <div style="position:absolute;left:55px">
                    <font style="font-weight:bold;color:black;position:absolute;left:0px">Add description to the book:&nbsp</font><br/>
                  <textarea rows="4" cols="50" name="description" form="uploadform" style="color:black" ></textarea>
                 </div>
                
                <div style="position:absolute;left:0px;bottom:15px">
                  <span class="input-group-btn">
                      <button type="submit" class="btn btn-four"><font style="font-weight:bold;">Upload</font></button>
                  </span>
                </div>
            </form>
            
        </header>
    
    
    <!--<form action="fileentry/add" method="post" enctype="multipart/form-data" class="jumbotron hero-spacer" style="width: 400px; margin:0 auto;border: 0px solid white;">

        
        <label for="exampleInputFile"><br/><h3><font color="black">Upload New PDF File</font></h3></label>
        <hr/>
        <input type="file" name="filefield" value="{{ csrf_token() }}" style="">
         <hr/>
                                      
         <button type="submit" class="btn btn-primary">Upload</button>
    </form>-->

    <hr style="width:1250px;">
        <div class="row">
            <div class="col-lg-12">
                <h3>Library</h3>
            </div>
        </div>
         <?php
            $countNum = 0;
            $filenameArr = array();
        ?>
        
<div class="row" style="margin:0 auto;">
    <ul class="thumbnails">
     @foreach($entries as $entry)
        <?php
            $countNum ++;
            array_push($filenameArr,$entry->filename);
            $container = "container".$countNum;
        ?>
         <div class="col-md-4">
            <div class="thumbnail">
                <div id=<?php echo $container?> style=""></div>
                <div class="caption">

                    <?php $fileName = $entry->original_filename; ?>
                    @if(strlen($fileName) > 30)
                    <p>{{substr($fileName,0,30)."..." }}</p>
                    @else
                    <p>{{$fileName}}</p>
                    @endif
                    <a href="{{route('getentry', $entry->filename)}}">View</a><br/>
                    @if (Auth::user()->isAdmin)
                    {{ Form::open(array('method'
                    => 'DELETE', 'route' => array('deleteentry', $entry->filename))) }}
                    {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                    {{ Form::close() }}
                    @else

                    @endif
                </div>
            </div>
        </div>
    @endforeach
    </ul>
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
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array[y - 1];
        var divId = "container" + y;
        getPreview(url, divId);
    }             
               
</script> 


@endsection