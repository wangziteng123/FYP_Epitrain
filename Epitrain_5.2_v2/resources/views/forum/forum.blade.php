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
use Illuminate\Notifications\Notifiable;

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

    $topFiveTags = \DB::table('forumtags')
        -> orderBy ('count', 'DESC')
        -> select ('forum_tag')
        -> get();

?>
<?php
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $parts = parse_url($url);
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
    } else {
        $thisSortField = "";
        $thisCount = 0;
    }
    //exit(var_dump($tagsForSearch) . var_dump($query));
    if(isset($query['studentInput']) && !isset($studentInput)) {
      $studentInput = $query['studentInput'];
    } else if (!isset($studentInput)) {
      $studentInput = "";
    }

    if(isset($query['sortField']) && !isset($thisSortField)) {
      $thisSortField = $query['sortField'];
    } else if (!isset($thisSortField)) {
      $thisSortField = "";
    }

    if(isset($query['count']) && !isset($thisCount)) {
      $thisCount = $query['count'];
    } else if (!isset($thisCount)) {
      $thisCount = "";
    }
    if(!isset($tagsForSearch)) {
      $tagsForSearch = [];
    }
?>

<div class="col-md-3 col-s-12 center-block">
  <h1 style="position: static;left: 14px;">Discussion Forum</h1>
  <hr>
  <div style="position:static;left:15px;">
  	<button class="btn btn-raised btn-primary initialism slide_open" style = "font-size:14px"><i class="fa fa-plus-circle" aria-hidden="true"></i> NEW DISCUSSION</button>
  </div>
  <div class="row" style="position:static; left:15px; " >
    <div class="panel panel-primary col-md-11 col-md-offset-1">
        <div class="panel-heading">
                 Search tags
            </div>

             <div class="panel-body">
                    
                <form action=<?php echo URL::route('filterTags');?> method="get" class="form-horizontal">
                    <div class="form-group">
                        <label for="studentInput" class="col-md-2 control-label">Tag</label>

                    <div class="col-sm-10 col-xs-10">
                        <input type="text" class="form-control" id="studentInput" name="studentInput" placeholder="Tag to search">
                    </div>
                    <div class="form-group">
                         <div class="col-sm-2 col-xs-2 col-xs-offset-2 col-sm-offset-3">
                            <input type="submit" class="btn btn-raised btn-info" value="Search">
                          </div>
                    </div>
                    </div>
                </form>
                @if($tagsForSearch!==null && $tagsForSearch !== "")

                          <?php $counter1 = 1; ?>
                        @foreach($tagsForSearch as $tag)
                            <?php 
                                $tagsToPass = substr($tag->forum_tag,1);
                                $forumShowTagPosts = URL::route('forumShowTagPosts');
                                $forumShowTagPosts = $forumShowTagPosts."?id=".$tagsToPass;
                                if($counter1 <6){ 
                            ?>            
                                <font color='black'><a style="text-decoration: none" href=<?php echo $forumShowTagPosts; ?>>
                                    <button type="button" class="btn btn-secondary btn-sm"><?php echo $tag->forum_tag;?></button>
                                    </a>
                                </font>
                            <?php } ?>
                            <?php $counter1 = $counter1 + 1; ?>
                            
                            </br>
                        @endforeach
                @endif
              </div>
          </div>
      </div>
    <br/>
  <div style="position:static; left:px; " >
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
<div class="col-md-9 col-s-12 center-block">
    <div style="position:static; left:px; " >
    Sort discussion by: </br>
      <form method="get" id="sortForm" action=<?php echo URL::route('discussionSort');?>>
          <input type="hidden" id="sortField" name="sortField" value=""> 
          <input type="hidden" id="oldValue" name="oldValue" value="<?php echo $oldValue;?>">
          <input type="hidden" id="count" name="count" value="<?php echo $count;?>">
          @if ($studentInput != null)
            <input type="hidden" name="studentInput" value="<?php echo $studentInput;?>">
          @endif
          <input type="submit" value="Date" class="btn btn-primary btn-raised" onclick="populateField('date')"></input>
          <input type="submit" value="Category" class="btn btn-primary btn-raised" onclick="populateField('category')"></input>
          <input type="submit" value="Likes" class="btn btn-primary btn-raised" onclick="populateField('likes')"></input>
          <input type="submit" value="Views" class="btn btn-primary btn-raised" onclick="populateField('views')"></input>
      </form>
    </div>
    @foreach($discussions as $discussion)

    	<?php
	    	$forumpageUrl = URL::route('forumpage');
	    	$forumpageUrl = $forumpageUrl."?id=".$discussion->id;

            $isOpen= $discussion->isOpen; // needed to see if the discussion is still open for reply
	    	$showResponsePageUrl = URL::route('forumResponsePage');
	    	$showResponsePageUrl= $showResponsePageUrl."?id=".$discussion->id;
            
            $toSendToLike = array($showResponsePageUrl);
            
            $likes = DB::table('discussionUserLike')
                -> where ('discussion_id', '=', $discussion->id)
                -> count();
            
	    	$discussionId = $discussion->id;
            
	    	$responses = \DB::table('forumresponse')
            		->where('discussion_id', $discussionId)
            		->get();

            $numOfResponses = count($responses);
           //echo var_dump($numOfResponses);
               
            $tagCount = 0;
            $tagsDisc = \DB::table('forumtags_discussion')
            		->where('discussion_id', $discussionId)
            		->get();
            shuffle($tagsDisc);

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
    {{ $discussions->appends(['oldValue' => $oldValue, 'count' => $thisCount, 'sortField' => $thisSortField, 'studentInput' => $studentInput])->links() }}
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
</script>


@endsection