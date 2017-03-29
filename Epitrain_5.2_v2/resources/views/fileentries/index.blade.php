

@extends('layouts.app')

<link type="text/css" rel="stylesheet" href="css/fileentry.css"/>
@section('content')
<?php 

?>
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Manage Library</li>
        </ul>
    </div>
</div>
@if(Session::has('failure'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('failure') }}</p>
    </div>
@endif
@if(Session::has('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('success') }}</p>
    </div>
@endif
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
                    <div class = "input-group col-md-9">
                      <input type="text" readonly class="form-control" placeholder="Select file to upload" style ="font-size:18px">
                      <input type="file" name="filefield" value="{{ csrf_token() }}" style="color:black" accept="application/pdf" required>
                    </div>
                  <!--<div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Select a file to upload" style="color:black;font-size:16px">
                  </div>-->
                </div>
             
              <div class="form-group">
                <label for="selectCat" class ="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Category</label>
                <div class = "col-md-10">
                    <select name="category" style="font-size:14px" id = "selectCat" class="form-control" placeholder="Choose ebook category">
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
                <textarea class="form-control" rows="3" id="description" name="description" form="uploadform" placeholder="Enter description of ebook"></textarea>
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
    
        <div class="row">
            <div class="col-sm-12">
                <h2 style="color:black">Library</h2>
            </div>
            <br/>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <span style="color:black" class= "col-sm-4 col-sm-offset-4">Sort files by: 
                      <form method="post" id="sortForm" action=<?php echo URL::route('fileSort');?>>
                          <input type="hidden" id="mode" name="mode" value="<?php echo $mode;?>">
                          <input type="hidden" id="sortField" name="sortField" value="">
                          <input type="submit" value="Name" class="btn btn-primary btn-raised" onclick="populateField('original_filename')"></input>
                          <input type="submit" value="Category" class="btn btn-primary btn-raised" onclick="populateField('category')"></input>
                          <input type="submit" value="Price" class="btn btn-primary btn-raised" onclick="populateField('price')"></input>
                      </form>
                </span>
                <span style="color:black" class= "col-sm-4 col-sm-offset-4">Filter files by category: 
                  <form method="post" id="filterForm" action=<?php echo URL::route('fileFilter');?>>
                    <input type="hidden" id="mode" name="mode" value="<?php echo $mode;?>">
                    <div class="form-group">
                      <div class = "col-sm-10 col-sm-offset-1">
                          <select name="filterCat" style="font-size:14px" id = "selectFilterCat" class="form-control" placeholder="Choose ebook category">
                            <option value="Trading" selected><font color="black" size = "3">Trading</font></option>
                            <option value="Risk Management"><font color="black" size = "3">Risk Management</font></option>
                            <option value="Fintech"><font color="black" size = "3">Fintech</font></option>
                            <option value="Project Management"><font color="black" size = "3">Project Management</font></option>
                            <option value="Finance"><font color="black" size = "3">Finance</font></option> 
                            <option value="Business Management"><font color="black" size = "3">Business Management</font></option>
                            <option value="Leadership"><font color="black" size = "3">Leadership</font></option>
                            <option value="Financial Market"><font color="black" size = "3">Financial market</font></option>
                            <option value=""><font color="black" size = "3">All categories</font></option>
                          </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class = "col-sm-10 col-sm-offset-1">
                        <input type="submit" class="btn btn-raised btn-success" value="Filter"></input>
                      </div>
                    </div>
                  </form>
                </span>
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
         <div class="col-sm-6 col-md-4" style="height:395px">
            <div class="thumbnail" style="height:95%">
                <div id=<?php echo $container?> style="height:42%"></div>
                <div class="caption">

                    <?php 
                      $fileName = $entry->original_filename; 
                      $fileCategory = $entry->category;
                      $filePrice = $entry->price;
                    ?>
                    @if(strlen($fileName) > 30)
                    <p style="font-size:14px"><strong>{{substr($fileName,0,30)."..." }}</strong></p>
                    @else
                    <p style="font-size:14px"><strong>{{$fileName}}</strong></p>
                    @endif
                    <p style="font-size:16px">Category: {{$fileCategory}}</p>
                    <p style="font-size:16px">Price: ${{$filePrice}}</p>
                    @if(strpos($fileName,'xls') !== false || strpos($fileName,'xlsx') !== false || strpos($fileName,'xlsm'))
                    <a href="{{route('downloadspreadsheet', $entry->filename)}}" class="btn btn-raised btn-success">Download</a><br/>
                    @else
                    <a href="{{route('getentry', $entry->filename)}}" class="btn btn-raised btn-info">View</a>
                    @endif

                    @if (Auth::user()->isAdmin)
                      <!-- Button trigger modal for adding category -->
                      <button type="button" class="btn btn-raised btn-warning" data-toggle="modal" data-target="#editModal" style = "font-size:14px" onclick="loadModal(<?php echo $fileName;?>, <?php echo $fileCategory;?>, <?php echo $filePrice;?>, <?php echo $entry->description;?>)">
                           Edit
                      </button>

                      {{ Form::open(array('method'
                      => 'DELETE', 'route' => array('deleteentry', $entry->filename))) }}
                      {{ Form::submit('Delete', array('class' => 'btn btn-raised btn-danger')) }}
                      {{ Form::close() }}
                    @endif
             
                </div>
            </div>
        </div>
    @endforeach
    </ul>
</div>

<!-- Modal for editing category -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <font color='black'> <h4 class="modal-title" id="myModalLabel">Edit book details</h4></font>
      </div>
      <div class="modal-body">
        <!-- Add a form inside the edit category modal-->
          <font color='black'> 
            <form action="fileentry/edit" id="editform" method="post" enctype="multipart/form-data" style="max-width: 100%; min-height: 480px;margin:0 auto; border: 0px solid white;" onsubmit="" class="form-horizontal">
              <legend><strong>Upload File</strong></legend>

                <input type="hidden" name="oldFileName" value=<?php echo($entry->filename);?>>
                <div class="form-group is-empty is-fileinput">
                  <!--<div class="btn" style="padding-top:0px !important">-->
                  <label for="inputFile" class="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Upload</label>   
                    <!--<span size = "3" >Upload</span>-->
                    <div class = "input-group col-md-9">
                      <input type="text" readonly class="form-control" placeholder="Select file to upload" style ="font-size:18px" id="existingFile" value="">
                      <input type="file" name="filefield" value="{{ csrf_token() }}" style="color:black" accept="application/pdf" value="">
                    </div>
                  <!--<div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Select a file to upload" style="color:black;font-size:16px">
                  </div>-->
                </div>
             
              <div class="form-group">
                <label for="selectCatEdit" class ="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Category</label>
                <div class = "col-md-10">
                    <select name="category" style="font-size:14px" id = "selectCatEdit" class="form-control" placeholder="">
                      <option value="Trading"><font color="black" size = "3">Trading</font></option>
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
                <label for="existingPrice" class ="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Price</label>
                <div class = "col-md-10">
                  <input type="number" name="price" id="existingPrice" step="any" style="font-size:16px" placeholder="Enter ebook price" class="form-control" value="">
                </div>
              </div>
                 
              <div class="form-group is-empty">
                <label for="existingDescription" class ="col-md-2 control-label" style ="color:midnightblue;font-size:14px">Description</label>
                <div class = "col-md-10">
                <textarea class="form-control" rows="3" id="existingDescription" name="description" form="uploadform" placeholder="Enter description of ebook" value=""></textarea>
                </div>
              </div>
                
              <div class="form-group">
                <div class = "col-md-9 col-md-offset-2">
                  <button type="submit" class="btn btn-raised" style="background-color: darkblue; color:white">Update<div class="ripple-container"></div></button>
                </div>
              </div>
            </form>
          </font>
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
    var countEntries = <?php echo count($entries);?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;
    if (mainUrl == "localhost") {
      mainUrl = "localhost:8000"
    }
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array[y - 1];
        var divId = "container" + y;
        getPreview(url, divId);
    }             
               
</script> 
<script>
function populateField(fieldToSort){
    document.getElementById('sortField').value=fieldToSort;
}
function loadModal(filename, category, price, description){
    document.getElementById('existingFile').value = filename;
    document.getElementById('selectCatEdit').placeholder = category;
    document.getElementById('existingPrice').value = price;
    document.getElementById('existingDescription').value = description;
}
</script>
@endsection