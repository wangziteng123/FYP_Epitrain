@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Discussion Forum</li>
        </ul>
    </div>
</div>
<?php
	$categories = \DB::table('category')
      ->where('shownInForumCat','=',1)
      ->get();
    
    if(empty($discussions)){
        $discussions = \DB::table('forumdiscussion') 
            ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
            ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
            ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
            ->orderBy('created_at', 'desc') //Added This
            ->paginate(5);
    }
    
    if(empty($oldValue)){
       $oldValue = ""; 
    }
    if(empty($count)){
        $count = 0;
    }

  $user = \DB::table('users')->where('id', Auth::user()->id)->value('id');

  //Added Here
  
  $topFiveTags = \DB::table('forumtags')
        -> orderBy ('count', 'DESC')
        -> select ('forum_tag')
        -> get();
    
    //To Here
  
?>

@if (session()->has('flash_notification.message'))
       <div class="alert alert-{{ session('flash_notification.level') }}">
           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

           {!! session('flash_notification.message') !!}
       </div>
@endif


<div class="col-lg-3 col-md-3 col-s-12 center-block" >
    <h1 style="position: static;left: 14px;">Discussion Forum (Admin)</h1>
    <hr>
  	<button class="btn btn-raised btn-primary initialism slide_open" style = "font-size:14px"><i class="fa fa-plus-circle" aria-hidden="true"></i>  NEW DISCUSSION</button>
  	<br/><br/>
  <!-- Button trigger modal for adding category -->
    <!--<button type="button" class="btn btn-raised btn-success" data-toggle="modal" data-target="#myModal" style = "font-size:14px">
         Add Category
    </button>
    <button type="button" class="btn btn-raised btn-info" data-toggle="modal" data-target="#myEditModal" style = "font-size:14px">
         Edit Category
    </button>-->

    <div class="row" style="position:static; left:15px; " >

    <form class="typeahead navbar-form navbar-left" role="search">
        <div class="form-group">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="search" name="q1" class="search-input2 form-control" placeholder="Search for tags" autocomplete="off" style="width:235px;color:black;">
        </div>
    </form>
  </div>
    
    <div style="position:static; left:15px; " >
    Top Five Tags </br>
        <?php $counter = 1; ?>
        @foreach($topFiveTags as $tags)
            <?php 
                $tagsToPass = substr($tags->forum_tag,1);
                $forumShowTagPosts = URL::route('forumShowTagPosts');
                $forumShowTagPosts = $forumShowTagPosts."?id=".$tagsToPass;
                if($counter <6){ 
            ?>            
                <font color='black'><a style="text-decoration: none" href=<?php echo $forumShowTagPosts; ?>>
                    <button type="button" class="btn btn-secondary btn-sm"><?php echo $tags->forum_tag;?></button>
                    </a>
                </font>
            <?php } ?>
            <?php $counter = $counter + 1; ?>
            
            </br>
        @endforeach
    </div>
</div>

<!-- Modal for adding category -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <font color='black'> <h4 class="modal-title" id="myModalLabel">Add Category</h4></font>
      </div>
      <div class="modal-body">
        <!-- Add a form inside the add category modal-->
           <font color='black'> <form method="post" id="addCategory" action=<?php echo URL::route('addCategory');?>>
            Category Name: <input type="text" name="categoryName" class="form-control" >


        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

        <input type="submit" value="Add Category" class="btn btn-primary"></button>

        </div>
        </form></font>
      </div>

    </div>
  </div>
</div>
<!--Added Here-->
<!-- Modal for editing category -->
<div class="modal fade" id="myEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <font color='black'> <h4 class="modal-title" id="myModalLabel">Edit Category</h4></font>
      </div>
      <div class="modal-body"><form method="post" id="editCategory" action=<?php echo URL::route('editCategory');?>>
        Choose Category Name to Edit:<br/>
        <select name="category" required>
          @foreach($categories as $category)
            <a href="#"><font size="3"><?php echo $category->categoryname;?></font></a><br/>
              <option value=<?php echo $category->id;?> style=""><?php echo $category->categoryname;?></option>
          @endforeach
        </select>
        <br/><br/>
        <!-- Add a form inside the add category modal-->
           <font color='black'> 
            New Category Name: <input type="text" name="categoryName" class="form-control" >


        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

        <input type="submit" value="Edit Category" class="btn btn-primary"></button>

        </div>
        </form></font>
      </div>

    </div>
  </div>
</div>
<!--To Here-->
    	<!--
    	<a href="#"><font size="3" style="color:white">All Discussion</font></a> -->
        </div></div>
</div>
<div class="col-lg-9 col-md-9 col-s-12 center-block">
    
    <div style="position:static; left:px; " >
    Sort discussion by: </br>
      <form method="get" id="sortForm" action=<?php echo URL::route('discussionSort');?>>
          <input type="hidden" id="sortField" name="sortField" value=""> 
          <input type="hidden" id="oldValue" name="oldValue" value="<?php echo $oldValue;?>">
          <input type="hidden" id="count" name="count" value="<?php echo $count;?>">
          <input type="submit" value="Date" class="btn btn-primary btn-raised" onclick="populateField('date')" style="background-color:#01466f"></input>
          <input type="submit" value="Category" class="btn btn-primary btn-raised" onclick="populateField('category')"style="background-color:#01466f"></input>
          <input type="submit" value="Likes" class="btn btn-primary btn-raised" onclick="populateField('likes')" style="background-color:#01466f"></input>
          <input type="submit" value="Views" class="btn btn-primary btn-raised" onclick="populateField('views')" style="background-color:#01466f"></input>
      </form>
    </div>
    
    	@foreach($discussions as $discussion)

    	<?php
	    	$forumpageUrl = URL::route('forumpage');
	    	$forumpageUrl = $forumpageUrl."?id=".$discussion->id;


        $isOpen= $discussion->isOpen; // needed to see if the discussion is still open for reply

	    	$showResponsePageUrl = URL::route('forumResponsePage');
	    	$showResponsePageUrl= $showResponsePageUrl."?id=".$discussion->id;
        
        $likes = DB::table('discussionUserLike')
                -> where ('discussion_id', '=', $discussion->id)
                -> count();

	    	$discussionId = $discussion->id;
        

	    	$responses = \DB::table('forumresponse')
            		->where('discussion_id', $discussionId)
            		->get();

            $numOfResponses = count($responses);
           //echo var_dump($numOfResponses);
            
            //Added Here
            
            $tagCount = 0;
            $tagsDisc = \DB::table('forumtags_discussion')
            		->where('discussion_id', $discussionId)
            		->get();
            shuffle($tagsDisc);
           //To Here
        
        ?>


	    <div class="jumbotron" >
	        <div class="row center-block">
                <div class="col-sm-8 center-block"><font color='black'><h2>
                    <b><?php echo app('profanityFilter')->filter($discussion->title);?></b></h2></font>

                    <font color='black'><h4><b>Category:</b> <?php 
                    echo $discussion->categoryname; 
                    ?></h4></font>

	    		    <font color='black'><b>Posted by:</b> <?php echo $discussion->name;?></font>


	    			<font color='grey'><?php  $creationDate= $discussion->created_at;

	    			$discussionId = $discussion->id;

                    $d= strtotime($creationDate);

                    $convertD = date("Y-m-d h:i:sa", $d);
                    $convertDToDateFormat= date_create($convertD);
                     $currentDate = date("Y-m-d h:i:sa");
                                      $currentDateToDateFormat= date_create($currentDate);
                            $dateDifference=date_diff($convertDToDateFormat,$currentDateToDateFormat);
                             $dateDiff=   $dateDifference ->format("%a days ago ");

                              if($dateDiff == "0 days ago "){
                              $timeDiffInMinutues = $dateDifference ->format("%i minutes ago ");
                                   echo $timeDiffInMinutues;
                              }
                               else{
                                   echo $dateDiff;

                               }

                               ;?></font><br/><br/>
	    			<font color='black'>
                <?php $desc = app('profanityFilter')->filter($discussion->description);?>
              @if(strlen($desc) < 170)
                <font color='black' size='4'><?php echo $desc;?></font>
              @else
                <font color='black' size='4'>{{substr($desc,0,170)}}</font>
                <a style="font-size:20px" href=<?php echo $forumpageUrl?> data-toggle="tooltip" data-placement="bottom" title="Click here to view the whole post">
                    ...
                </a>
              @endif
              
            </font> <br/><br/>

	    			<?php
                //check if the discussion has been closed or not
                if ($isOpen == 0){ ?>
                    <a style="display:block; font-size:20px" href=<?php echo $forumpageUrl?>>
                    Reply <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    </font><br/>
                    <!-- this is to close the discussion-->
                    <form method="post" action=<?php echo URL::route('closeDiscussion');?>>
                    <input type="hidden" name="discussionId" value=<?php echo $discussionId;?>>
                    <input type="hidden" name="forumpageUrl" value=<?php echo $forumpageUrl;?>>
                    <button type="submit" class="btn btn-raised btn-warning">Close Discussion</button>
                    </form></br>

                    <?php

                }else{ ?>
                    <font color='red'> This discussion has been closed </font>

                <?php
                }

            ?>

            <!-- Button trigger modal for deleting discussion -->
            <button type="button" class="btn btn-danger btn-raised" data-toggle="modal" data-target="#myModalDeleteDiscussion" onclick="loadModal( <?php echo $discussionId;?>)">
                 Delete Discussion
            </button>

            <!-- Modal for deleting discussion -->
            <div class="modal fade" id="myModalDeleteDiscussion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <font color='black'> <h4 class="modal-title" id="myModalLabel"><b>Delete Discussion</b></h4></font>
                  </div>
                  <div class="modal-body">
                    <!-- Add a form inside the add category modal-->
                       <font color='black'> <form method="post" id="deleteForm" action=<?php echo URL::route('deleteDiscussion');?>>
                        Are you sure you want to delete this discussion?
                        <input type="hidden" id="passDiscussionID" name="discussionId" value="">

                    <div class="modal-footer">
                      <button class="btn btn-raised btn-default" data-dismiss="modal">No</button>

                      <input type="submit" value="Yes" class="btn btn-raised btn-danger"></input>

                    </div>
                    </form></font>


                  </div>

                </div>
              </div>
            </div>

	    	    </div>
	    	    <div class="col-sm-4 center-block">
            <font color='black' style="font-size:34px">
              <a href="#" data-toggle="tooltip" data-placement="bottom" title="Number of views" style="color:black">
                <i class="fa fa-eye" aria-hidden="true" style="color:navy"></i>
                <?php echo $discussion->views; ?>
              </a>
            </font>

            <font color='black' style="font-size:34px"><a style="color:black" href=<?php echo $showResponsePageUrl?> data-toggle="tooltip" data-placement="bottom" title="Number of responses">
              <i class="fa fa-comment-o" style="color:navy"></i>
                <?php echo '' + $numOfResponses ?>
              </a> 
            </font>

            <font color='black' style="font-size:34px"><a style="color:black" href="{!! route('like', ['discussionId' => $discussion->id, 'userId' => $user]) !!}" data-toggle="tooltip" data-placement="bottom" title="Number of likes">
              <i class="fa fa-thumbs-up" style="color:navy" aria-hidden="true"></i>
              <?php echo $likes ?>
              </a>
            </font>
          </div>
	    	  <br/>
              
            @foreach($tagsDisc as $tags)
                <?php 
                    $tagsToPass = substr($tags->forum_tag,1);
                    $forumShowTagPosts = URL::route('forumShowTagPosts');
                    $forumShowTagPosts = $forumShowTagPosts."?id=".$tagsToPass;
                    if($tagCount <6){ 
                ?>            
                    <font color='black'><a style="text-decoration: none" href=<?php echo $forumShowTagPosts; ?>>
                        <button type="button" class="btn btn-secondary btn-sm" style="background: silver"><?php echo $tags->forum_tag;?></button>
                        </a>
                    </font>
                <?php } ?>
                <?php $counter = $tagCount + 1; ?>
                
                </br>
            @endforeach

	    	  </div>
        </div>  
    	@endforeach
      <?php
          $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          $parts = parse_url($url);
          if (isset($parts['query'])) {
              parse_str($parts['query'], $query);
              $thisSortField = $query['sortField'];
              $thisCount = $query['count'];
          } else {
              $thisSortField = "";
              $thisCount = 0;
          }

      ?>
      {{ $discussions->appends(['oldValue' => $oldValue, 'count' => $thisCount, 'sortField' => $thisSortField])->links() }}
    </div>




<!-- Slide in popup window-->

<div id="slide" class="well col-sm-7 col-sm-offset-2 col-xs-9 col-xs-offset-1" style="color: black">
  <button class="slide_close btn btn-default" style="position:absolute;right:20px"><i class="fa fa-times" aria-hidden="true"></i></button>
    <br/>
    <form action=<?php echo URL::route('createDiscussion');?> method="post">
      Title of Discussion:<br/>
    <input type="text" name="title" required><br/>
    <br/>
    Choose Category:<br/>
    <select name="category" required>
      @foreach($categories as $category)
        <a href="#"><font size="3"><?php echo $category->categoryname;?></font></a><br/>
          <option value=<?php echo $category->id;?> style=""><?php echo $category->categoryname;?></option>
      @endforeach
    </select>
    
    <div class="form-group">
      <label for="textArea" class="col-md-4 control-label" style="padding-left: 0px"><font size="3">Discussion Content</font></label>
      <textarea class="form-control" rows="3" id="textArea" name="description"></textarea><br/>
    </div>
    <input type="submit" value="Submit" class="btn btn-raised btn-success" style="float:right">
    <button class="btn btn-raised btn-warning slide_close" style="float:right">Cancel</button>
  </form>
</div>


<script>
$(document).ready(function () {

    $('#slide').popup({
        focusdelay: 400,
        outline: true,
        vertical: 'top'
    });

});
function populateField(fieldToSort){
    document.getElementById('sortField').value=fieldToSort;
}
function loadModal(discussion_id){
    document.getElementById('passDiscussionID').value=discussion_id ;
}


</script>


 <?php 

      $url = URL::route('forumShowTagPosts');
    ?>
    <script>
        jQuery(document).ready(function($) {
            // Set the Options for "Bloodhound" suggestion engine
            var mainUrl = window.location.hostname;
            var passUrl = <?php echo $url."?id="?>;

            var engine2 = new Bloodhound({
                remote: {
                    url: '/findForumtag?q1=%QUERY%',
                    wildcard: '%QUERY%',
                },
                datumTokenizer: Bloodhound.tokenizers.whitespace('q1'),
                queryTokenizer: Bloodhound.tokenizers.whitespace
            });

            engine2.initialize();

            $(".search-input2").typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                source: engine2.ttAdapter(),
                // This will be appended to "tt-dataset-" to form the class name of the suggestion menu.
                name: 'forumtag_list',
                displayKey: 'forum_tag',
                // the key from the array we want to display (name,id,email,etc...)
                templates: {
                    empty: [
                        '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                    ],
                    header: [
                        '<div class="list-group search-results-dropdown">'
                    ],
                    suggestion: function (data) {                                       
                        return '<div class="user-search-result" style="color:black"><font color="black"><a style="text-decoration: none" href='+passUrl.concat(data.forum_tag.substring(1))+'>'
                            +'<button type="button" class="btn btn-secondary btn-sm">'+data.forum_tag+'</button>
                            </a>
                        </font></div>';
              }
                }
            });

            });
    </script>


@endsection