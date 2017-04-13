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
      $categories = \DB::table('category')
        ->where('shownInEbookCat','=','1')
        ->get();

?>
<div class="container" >

 <div class="col-sm-12">

                <!-- Blog Post -->

          <!-- Title -->
					<div class="row">
						<h1>My Library</h1>
					</div>
          <div class="row">

            <span style="color:black" class= "col-sm-5">
              <form method="get" id="sortForm" action=<?php echo URL::route('libSort');?>>
                  <input type="hidden" id="mode" name="mode" value="<?php echo $mode;?>">
                  <input type="hidden" id="sortField" name="sortField" value="">
                  Sort ebooks by: 
                  <input type="submit" value="Name" class="btn btn-primary btn-raised" onclick="populateField('original_filename')"></input>
                  <input type="submit" value="category" class="btn btn-primary btn-raised" onclick="populateField('category')"></input>
              </form>
            </span>
            <span style="color:black" class= "col-sm-7">
                <form action=<?php echo URL::route('filterLibrary');?> method="get" class="form-inline">
                  <input type="hidden" id="mode" name="mode" value="<?php echo $mode;?>">
                  <?php
                    $modeArr = explode("-", $mode);
                    $sortField = $modeArr[0];
                  ?>
                  <input type="hidden" name="sortField" value="<?php echo $sortField;?>">
                  
                  <div class="col-sm-2 col-xs-2 form-group">
                      Category:
                  </div>
                  <div class="form-group col-sm-3">                      
                      <select name="category" id="ebookCat" style="font-size:14px" class="form-control" placeholder="Choose ebook category">
                        @foreach($categories as $category)
                            <?php
                              $splitCat = explode(' ',$category->categoryname);
                              $tempCat = "";
                              foreach($splitCat as $word) {
                                  $tempCat .= $word . "_";
                              }
                            ?>
                            <option value=<?php echo $tempCat;?>><font color="black" size = "3"><?php echo $category->categoryname;?></font></option>
                        @endforeach
                      </select>
                  </div>
                  <div class="col-sm-2 col-xs-2 form-group">
                      Ebook name:
                  </div>
                  <div class="form-group col-sm-4">                      
                      <input type="text" class="form-control" id="materialsInput" name="ebookName" placeholder="Ebook to search">
                  </div>

                  <div class="form-group col-sm-1">
                      <input type="submit" class="btn btn-info btn-raised" value="Search">
                  </div>

                </form>
            </span>
          </div>
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
                    ?>
              <div class="col-md-4 col-sm-6 col-xs-12 hero-feature" style="height:500px">

              <div class="thumbnail" style="height:95%">
                <div id=<?php echo $container;?> style="height:42%"></div>

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
<?php $thisSortField = explode("-", $mode)[0]; ?>
{{ $entries->appends(['sort' => $mode, 'sortField' => $thisSortField])->links() }}


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
</script>

@endsection