

@extends('layouts.app')

<link type="text/css" rel="stylesheet" href="css/fileentry.css"/>
@section('content')
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Manage Library</li>
        </ul>
    </div>
</div>
@if (session()->has('flash_notification.message'))
       <div class="alert alert-{{ session('flash_notification.level') }}">
           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

           {!! session('flash_notification.message') !!}
       </div>
@endif


<h1><font color='black'>Manage Library</h1>

    <!-- Jumbotron Header -->
        <header class="jumbotron col-md-6 col-md-offset-3">
          <div class="container">
            <form action="fileentry/add" id="uploadform" method="post" enctype="multipart/form-data" style="max-width: 100%; min-height: 480px;margin:0 auto; border: 0px solid white;" onsubmit="" class="form-horizontal">
              <legend><strong>Upload New File</strong></legend>
            
                <div class="form-group is-empty is-fileinput">
                  <!--<div class="btn" style="padding-top:0px !important">-->
                  <label for="inputFile" class="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Upload</label>   
                    <!--<span size = "3" >Upload</span>-->
                    <div class = "col-md-9">
                      <input type="text" readonly class="form-control" placeholder="Select file to upload" style ="font-size:18px">
                      <input type="file" id="input-file" accept=".pdf">
                    </div>
                  <!--<div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Select a file to upload" style="color:black;font-size:16px">
                  </div>-->
                </div>
             
              <div class="form-group">
                <label for="selectCat" class ="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Category</label>
                <div class = "col-md-10">
                    <select style="font-size:14px" id = "selectCat" class="form-control" placeholder="Choose ebook category">
                      <option value="Trading" selected><font color="black" size = "3">Trading</font></option>
                      <option value="Risk Management"><font color="black" size = "3">Risk Management</font></option>
                      <option value="Fintech"><font color="black" size = "3">Fintech</font></option>
                      <option value="Project Management"><font color="black" size = "3">Project Management</font></option>
                      <option value="Finance"><font color="black" size = "3">Finance</font></option> 
                      <option value="Business Management"><font color="black" size = "3">Business Management</font></option>
                      <option value="Leadership"><font color="black" size = "3">Leadership</font></option>
                      <option value="Financial Market"><font color="black" size = "3">Financial market</font></option>
                    </select>
                </div>
              </div>
                
              <div class="form-group is-empty">
                <label for="price" class ="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Price</label>
                <div class = "col-md-10">
                  <input type="number" name="price" id="price" step="any" style="font-size:16px" placeholder="Enter ebook price" class="form-control">
                </div>
              </div>
                 
              <div class="form-group is-empty">
                <label for="description" class ="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Description</label>
                <div class = "col-md-10">
                <textarea class="form-control" rows="3" id="description" placeholder="Enter description of ebook"></textarea>
                </div>
              </div>
                
              <div class="form-group">
                <div class = "col-md-9 col-md-offset-1">
                  <button type="submit" class="btn btn-raised" style="background-color: darkblue; color:white">Upload<div class="ripple-container"></div></button>
                </div>
              </div>
            </form>
          </div>
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
                <h3 style="color:black">Library</h3>
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

                    <?php 
                      $fileName = $entry->original_filename; 
                      $fileCategory = $entry->category;
                    ?>
                    @if(strlen($fileName) > 30)
                    <p style="font-size:14px"><strong>{{substr($fileName,0,30)."..." }}</strong></p>
                    @else
                    <p style="font-size:14px"><strong>{{$fileName}}</strong></p>
                    @endif
                    <p style="font-size:16px">Category: {{$fileCategory}}</p>
                    @if(strpos($fileName,'xls') !== false || strpos($fileName,'xlsx') !== false || strpos($fileName,'xlsm'))
                    <a href="{{route('downloadspreadsheet', $entry->filename)}}" class="btn btn-raised btn-success">Download</a><br/>
                    @else
                    <a href="{{route('getentry', $entry->filename)}}" class="btn btn-raised btn-info">View</a><br/>
                    @endif

                    @if (Auth::user()->isAdmin)
                    {{ Form::open(array('method'
                    => 'DELETE', 'route' => array('deleteentry', $entry->filename))) }}
                    {{ Form::submit('Delete', array('class' => 'btn btn-raised btn-danger')) }}
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