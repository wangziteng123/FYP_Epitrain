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
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use App\Notifications\SubscriptionExpiring;

    $user = Auth::user();
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
        ->where('user_id', $user->id)
        ->where('fileentry_id', $id)
        ->get();

     $libraryExist = \DB::table('libraries')
        ->where('user_id', $user->id)
        ->where('fileentry_id', $id)
        ->get();

    $coursesOfThisBook = \DB::table('courseMaterial')
        ->join('course', function ($join) use ($id) {
            $join->on('course.courseID', '=', 'courseMaterial.courseID')
                 ->where('courseMaterial.fileEntriesID', '=', $id)
                 ->where('course.isActive','=','1');
            })
            ->distinct()
            ->pluck('courseMaterial.courseID');

    $coursesOfThisUser = \DB::table('enrolment')
    ->where('userID', $user->id)
    ->where('isActive','=',1)
    ->pluck('courseID');

    $isStudent = false;
    foreach ($coursesOfThisBook as $course) {
        if(in_array($course, $coursesOfThisUser)) {
           $isStudent = true;
           break;
        }
    }

    $isSubscribe = Auth::user()->subscribe;
?>

@if($isSubscribe)
  <?php
    $currentTime = Carbon::now();
    $userSubscribe = \DB::table('subscription')
      ->where('user_id', $user->id)
      ->orderBy('id','desc')
      ->first();

    $end_Date = Carbon::createFromFormat('Y-m-d H:i:s', $userSubscribe->end_date);
    
    $expiring = $currentTime->diffInHours($end_Date);
    
    $informedEnding = $userSubscribe->informed_ending;
    $homeUrl = URL::route('home');
    
    if($expiring <= 168){
        if($informedEnding == 0){                    
            $user->notify(new SubscriptionExpiring($homeUrl));
            DB::table('subscription')
                -> where ('user_id', '=', $user->id)
                -> update(['informed_ending' => 1]);
        }
    } elseif($expiring > 168 && $userSubscribe->informed_ending == 1){
        DB::table('subscription')
            -> where ('user_id', '=', $user->id)
            -> update(['informed_ending' => 0]);
    }
    
    $notYetExpired = $currentTime->lt($end_Date);
?>
@endif
<div class="col-sm-12 col-xs-12" style="position:relative">

 <div class="col-sm-10 col-xs-10" style="position:relative">

    <!-- Blog Post -->

    <!-- Title -->
    <h1 style="position: absolute;left: 14px;">Search Results:</h1>
    <br/><br/><br/>
    <hr>

    <!--display search result-->
    @if($isSubscribe || $isStudent)
      <!-- if user is a subscriber -->
      @if($isSubscribe)
        @if($notYetExpired)
          <div class="jumbotron" style="background:#E1DFDE">
            <div class = "row">
                <div id="searchContainer" class="col-sm-1 col-sm-offset-1 hidden-xs"></div>
                <div class="col-sm-7 col-xs-8 col-xs-offset-1">
                  <font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $original_filename;?></font>
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
              <div class="col-sm-4 col-xs-4 col-xs-offset-4 hidden-xs">
                  @if (count($libraryExist))
                    <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                        <font style="">Already Purchased</font>
                    </button>
                  @elseif (!Auth::user()->isAdmin)
                  <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post">
                      <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                      <input type="hidden" name="fidStr" value=<?php echo $id;?>>
                          <button class="btn btn-raised btn-warning">
                              <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                              Add To Library
                          </button>
                   </form>

                  @endif
              </div>
            </div>
            <div class = "row center-block visible-xs">
              <div class="col-sm-4 col-xs-4 col-xs-offset-4">
                @if (count($libraryExist))
                    <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                        <font style="">Already Purchased</font>
                    </button>
                  @elseif (!Auth::user()->isAdmin)
                  <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post">
                      <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                      <input type="hidden" name="fidStr" value=<?php echo $id;?>>
                          <button class="btn btn-raised btn-warning">
                              <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                              Add To Library
                          </button>
                   </form>
                  @endif
              </div>
            </div>
          </div>
        @else
          <div class="jumbotron" style="background:#E1DFDE">
            <div class = "row">
                <div id="searchContainer" class="col-sm-1 col-sm-offset-1 hidden-xs"></div>
                <div class="col-sm-7 col-xs-8 col-xs-offset-1">
                  <font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $original_filename;?></font>
                </div>
                <div class="col-sm-1 col-xs-1">
                  <font color="black" style="font-size:28px">S$<?php echo $price;?>
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
                    <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                        <font>Already Added</font>
                    </button>
                @else
                    <form action=<?php echo url('shoppingcart/add');?> method="post">
                        <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                        <input type="hidden" name="fid" value=<?php echo $id;?>>
                            <button  class="btn btn-raised btn-info">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                Add to Cart
                            </button>
                     </form>
                @endif
              </div>
              <div class="col-sm-3 col-xs-3 col-xs-offset-2 hidden-xs">
                  @if (count($libraryExist))
                    <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                        <font style="">Already Purchased</font>
                    </button>
                  @elseif (!Auth::user()->isAdmin)
                  <form action=<?php echo URL::route('payment');?> method="post">
                      <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                      <input type="hidden" name="fidStr" value=<?php echo $id;?>>
                      <input type="hidden" name="totalPrice" id="totalPrice" value="<?php echo $price;?>"/>
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
                    <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                        <font style="">Already Purchased</font>
                    </button>
                  @elseif (!Auth::user()->isAdmin)
                  <form action=<?php echo URL::route('payment');?> method="post">
                      <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                      <input type="hidden" name="fidStr" value=<?php echo $id;?>>
                      <input type="hidden" name="totalPrice" id="totalPrice" value="<?php echo $price;?>"/>
                      <button class="btn btn-raised btn-warning">
                          <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                          Buy Now
                      </button>
                   </form>

                  @endif
              </div>
            </div>
          </div>
        @endif
      @else 
        <!-- if user is a student -->
        <div class="jumbotron" style="background:#E1DFDE">
            <div class = "row">
                <div id="searchContainer" class="col-sm-1 col-sm-offset-1 hidden-xs"></div>
                <div class="col-sm-7 col-xs-8 col-xs-offset-1">
                  <font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $original_filename;?></font>
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
              <div class="col-sm-4 col-xs-4 col-xs-offset-4 hidden-xs">
                  @if (count($libraryExist))
                    <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                        <font style="">Already Purchased</font>
                    </button>
                  @elseif (!Auth::user()->isAdmin)
                  <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post">
                      <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                      <input type="hidden" name="fidStr" value=<?php echo $id;?>>
                          <button class="btn btn-raised btn-warning">
                              <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                              Add To Library
                          </button>
                   </form>

                  @endif
              </div>
            </div>
            <div class = "row center-block visible-xs">
              <div class="col-sm-4 col-xs-4 col-xs-offset-4">
                @if (count($libraryExist))
                    <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                        <font style="">Already Purchased</font>
                    </button>
                  @elseif (!Auth::user()->isAdmin)
                  <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post">
                      <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                      <input type="hidden" name="fidStr" value=<?php echo $id;?>>
                          <button class="btn btn-raised btn-warning">
                              <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                              Add To Library
                          </button>
                   </form>
                  @endif
              </div>
            </div>
          </div>
      @endif
    @elseif(!$user->isAdmin)
      <!-- if user is a normal user -->
      <div class="jumbotron" style="background:#E1DFDE">
        <div class = "row">
            <div id="searchContainer" class="col-sm-1 col-sm-offset-1 hidden-xs"></div>
            <div class="col-sm-7 col-xs-8 col-xs-offset-1">
              <font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $original_filename;?></font>
            </div>
            <div class="col-sm-1 col-xs-1">
              <font color="black" style="font-size:28px">S$<?php echo $price;?>
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
                <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                    <font>Already Added</font>
                </button>
            @else
                <form action=<?php echo url('shoppingcart/add');?> method="post">
                    <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                    <input type="hidden" name="fid" value=<?php echo $id;?>>
                        <button  class="btn btn-raised btn-info">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            Add to Cart
                        </button>
                 </form>
            @endif
          </div>
          <div class="col-sm-3 col-xs-3 col-xs-offset-2 hidden-xs">
              @if (count($libraryExist))
                <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                    <font style="">Already Purchased</font>
                </button>
              @elseif (!Auth::user()->isAdmin)
              <form action=<?php echo URL::route('payment');?> method="post">
                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                  <input type="hidden" name="fidStr" value=<?php echo $id;?>>
                  <input type="hidden" name="totalPrice" id="totalPrice" value="<?php echo $price;?>"/>
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
                <button class="btn btn-warning" style="background-color: darkblue; color: yellow">
                    <font style="">Already Purchased</font>
                </button>
              @elseif (!Auth::user()->isAdmin)
              <form action=<?php echo URL::route('payment');?> method="post">
                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                  <input type="hidden" name="fidStr" value=<?php echo $id;?>>
                  <input type="hidden" name="totalPrice" id="totalPrice" value="<?php echo $price;?>"/>
                  <button class="btn btn-raised btn-warning">
                      <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                      Buy Now
                  </button>
               </form>
              @endif
          </div>
        </div>
      </div>
    @else
      <!-- if user is admin -->
      <div class="jumbotron" style="background:#E1DFDE">
          <div class = "row">
              <div id="searchContainer" class="col-sm-1 col-sm-offset-1 hidden-xs"></div>
              <div class="col-sm-7 col-xs-8 col-xs-offset-1">
                <font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $original_filename;?></font>
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
          <div class = "row">
              <a href="{{route('getentry', $filename)}}" class="btn btn-raised btn-info">View Document</a>
              <!-- Button trigger modal for adding category -->
            
              <?php echo '<button type="button" class="btn btn-raised btn-warning" data-toggle="modal" data-target="#editModal" onclick="loadModal(\'' . $filename . '\',\'' . $category . '\',\'' . $price . '\',\'' . $description . '\')" >Edit Details</button>'; ?>

              {{ Form::open(array('method'
              => 'DELETE', 'route' => array('deleteentry', $filename))) }}
              {{ Form::submit('Delete', array('class' => 'btn btn-raised btn-danger')) }}
              {{ Form::close() }}
          </div>
        </div>
    @endif
  </div>
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
            <form action=<?php echo URL::route('editentry');?> id="editform" method="post" style="max-width: 100%; min-height: 480px;margin:0 auto; border: 0px solid white;" onsubmit="" class="form-horizontal">
              <legend><strong>Edit book details</strong></legend>
              <div class="form-group">
                <input type="hidden" name="oldFileName" id= "existingFile" class="form-control" value="">
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
                <textarea class="form-control" rows="3" id="existingDescription" name="description" placeholder="Enter description of ebook" value=""></textarea>
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
    // URL of PDF document
    var mainUrl = window.location.hostname;
    var url = "http://" + mainUrl + "/fileentry/get/" + filename_array[0];
    var divId = "searchContainer";
    getPreview(url, divId);            
               
</script> 

@endsection