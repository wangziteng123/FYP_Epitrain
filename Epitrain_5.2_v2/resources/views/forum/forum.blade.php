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
	$categories = \DB::table('forumcategory') ->get();
	
	$discussions = \DB::table('forumdiscussion') 
		->join('forumcategory', 'forumdiscussion.category_id', '=', 'forumcategory.id')
		->join('users', 'forumdiscussion.user_id', '=', 'users.id')
        ->select('forumdiscussion.*', 'forumcategory.categoryname', 'users.name')
        ->paginate(5);

        $user = \DB::table('users')->where('id', Auth::user()->id)->value('id');



?>

<div class="col-md-3 col-s-12 center-block">
  <h1 style="position: static;left: 14px;">Discussion Forum</h1>
  <hr>
    <div style="position:static;left:15px;">
    	<button class="btn btn-raised btn-primary initialism slide_open" style = "font-size:14px"><i class="fa fa-plus-circle" aria-hidden="true"></i> NEW DISCUSSION</button>
    </div>
</div>
<div class="col-md-9 col-s-12 center-block">
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

	    ?>
    <div class="jumbotron">
      <div class="row center-block">

        <div class="col-sm-8 center-block"><font color='black'><h2>
            <?php $title = app('profanityFilter')->filter($discussion->title);?>
            <b><?php echo $title;?></b></h2></font>
            <font color='black'>
              <h4><b>Category:</b> 
                <?php 
                  echo $discussion->categoryname; 
                ?>
              </h4>
            </font>
  		      <font color='black' size="3"><b>Posted by:</b> <?php echo $discussion->name;?></font>
            <font color='grey' size="3"><?php  $creationDate= $discussion->created_at;
              $d= strtotime($creationDate);

              $convertD = date("Y-m-d h:i:sa", $d);
              $convertDToDateFormat= date_create($convertD);
              $currentDate = date("Y-m-d h:i:sa");
              $currentDateToDateFormat= date_create($currentDate);
              $dateDifference=date_diff($convertDToDateFormat,$currentDateToDateFormat);
              $dateDiff=   $dateDifference ->format("%a days ago ");

              if ($dateDiff == "0 days ago "){
              $timeDiffInMinutues = $dateDifference ->format("%i minutes ago ");
                   echo $timeDiffInMinutues;
              }
              else {
                   echo $dateDiff;
              }
            ;?></font><br/><br/><br/>
            <?php $desc = app('profanityFilter')->filter($discussion->description);?>
              @if(strlen($desc) < 170)
    			      <font color='black' size='4'><?php echo $desc;?></font>
              @else
                <font color='black' size='4'>{{substr($desc,0,170)}}</font>
                <a style="font-size:20px" href=<?php echo $forumpageUrl?> data-toggle="tooltip" data-placement="bottom" title="Click here to view the whole post">
                    ...
                </a>
              @endif
              <br/>
              </br>
            <?php
                //check if the discussion has been closed or not
                if ($isOpen == 0){ ?>
                    <a style="display:block; font-size:20px" href=<?php echo $forumpageUrl?>>
                 	      Reply 
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>  
                    </a>
                 	  <br/>
                 <?php

                }else{ ?>
                   <font color='red'> This discussion has been closed </font>

                <?php

                }

            ?>
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

  	  </div>
    </div>
    @endforeach
    {{ $discussions->links() }}
</div>




<!-- Slide in popup window-->

<div id="slide" class="well" style="position:relative;top:30px;width:600px;height:400px">
	<button class="slide_close btn btn-default" style="position:absolute;right:20px"><i class="fa fa-times" aria-hidden="true"></i></button>
  	<br/>
  	<form action=<?php echo URL::route('createDiscussion');?> method="post">
  		Title of Discussion:<br/>
		<input type="text" name="title" required><br/>
		<br/>
		Choose Category:<br/>
		<select name="category" required>
			@foreach($categories as $category)
    		<a href="#"><font size="3" style="color:white"><?php echo $category->categoryname?></font></a><br/>
    			<option value=<?php echo $category->id?> style=""><?php echo $category->categoryname?></option>
    		@endforeach
		</select>
		<br/><br/>
		<textarea class="materialize-textarea" name="description" rows="4" cols="50" required></textarea><br/>
		<input type="submit" value="Submit" style="position:absolute;right:35px;"><br/><br/>
		<button class="btn btn-default slide_close" style="float:right">Cancel</button>
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
</script>


@endsection