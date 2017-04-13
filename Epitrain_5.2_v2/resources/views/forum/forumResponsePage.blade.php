@extends('layouts.app')

@section('content')
<script>
  function goBack() {
      var url = window.location.href;
      var n = url.indexOf("forumResponsePage");
      url = url.substring(0,n);
      <?php if(Auth::user()->isAdmin) { ?>
        url = url.concat("forumAdmin");
      <?php } else { ?>
        url = url.concat("forum");
      <?php } ?>
      window.location.replace(url);
      //window.location.replace("http://localhost:8000/mylibrary");
      //window.alert(url);
  }       
</script>
<?php 
	$discussion = \DB::table('forumdiscussion') 
		->where('id', $discussionId)
		->get();

	$responses = \DB::table('forumresponse') 
		->where('discussion_id', $discussionId)
		->get();

?>
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <?php if(Auth::user()->isAdmin) { ?>
              <li style="font-size:16px"><a href="/forumAdmin">Discussion Forum</a></li>
            <?php } else { ?>
              <li style="font-size:16px"><a href="/forum">Discussion Forum</a></li>
            <?php } ?>
            <li style="font-size:16px" class="active">Forum Thread View</li>
        </ul>
    </div>
</div>

@foreach($discussion as $disc)
<div class="jumbotron" style='margin-left: 50px; margin-right: 50px'>
    <?php $title = app('profanityFilter')->filter($disc->title);?>
	<font color='black'><h2><b><?php echo $title;?></b></h2></font> <br/>
    <?php $desc = app('profanityFilter')->filter($disc->description);?>
	<font color='black' size='4'><?php echo $desc;?></font> <br/>
</div>
@endforeach




@foreach($responses as $response)


<div class="jumbotron responsiveSize" >

    <?php $cont = app('profanityFilter')->filter($response->content);?>
    <font color='black' size='4'><?php echo $cont;?> <br/>


	<?php

      $userID = $response->user_id;
      $userName = "";
        // getting the username from the userID
        if (Auth::check()) {
            $user_record = DB::table('users')->where('id', $userID)->first();
            $userName =  $user_record->name;

        }
  ?>
  <p style="font-size:16px">
    <?php echo "Posted by "?><strong>{{$userName}}</strong>
  </p>

    <font color='grey' size='3'>

    <?php

    $creationDate = $response->created_at;

	 $d= strtotime($creationDate);

     $convertD = date("Y-m-d h:i:sa", $d);
     $convertDToDateFormat= date_create($convertD);
     $currentDate = date("Y-m-d h:i:sa");
     $currentDateToDateFormat= date_create($currentDate);
     $dateDifference=date_diff($convertDToDateFormat,$currentDateToDateFormat);
     $dateDiff=   $dateDifference ->format(" %a days ago ");

     if($dateDiff == " 0 days ago "){
        $timeDiffInMinutues = $dateDifference ->format(" %i minutes ago ");
        echo $timeDiffInMinutues;
     }
      else{
        echo $dateDiff;

      }

	 ?></font> 
   <br/>
   @if (Auth::user()->isAdmin)
     <button type="button" class="btn btn-danger btn-raised" data-toggle="modal" data-target="#myModalDeleteComment" onclick="loadModal(<?php echo $response->id;?>, <?php echo $discussionId;?>)">
           Delete Comment
      </button>
   @endif
</div>
<!-- Modal for deleting comment-->
@if (Auth::user()->isAdmin)
  <div class="modal fade" id="myModalDeleteComment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <font color='black'> <h4 class="modal-title" id="myModalLabel"><b>Delete Discussion</b></h4></font>
        </div>
        <div class="modal-body">
          <!-- Add a form inside the add category modal-->
             <font color='black'> <form method="post" id="deleteForm" action=<?php echo URL::route('deleteComment');?>>
              Are you sure you want to delete this comment? Attached tags will also be deleted
              <input type="hidden" id="passCommentID" name="commentID" value="">
              <input type="hidden" id="passDiscussionID" name="discussionID" value="">

          <div class="modal-footer">
            <button class="btn btn-raised btn-default" data-dismiss="modal">No</button>

            <input type="submit" value="Yes" class="btn btn-raised btn-danger"></input>

          </div>
          </form></font>


        </div>

      </div>
    </div>
  </div>
@endif

@endforeach
<?php

    //Added This
    
    $forumpageUrl = URL::route('forumResponsePage');
    $forumpageUrl = $forumpageUrl."?id=".$discussionId;
    
    //To here

?>

<div class="responsiveSize">
  <form method="post" action=<?php echo URL::route('createResponse');?>>
    <input type="hidden" name="discussionId" value=<?php echo $discussionId;?>>
    <input type="hidden" name="user_id" value=<?php echo \Auth::user()->id;?>>
    <input type="hidden" name="forumpageUrl" value=<?php echo $forumpageUrl;?>>

    <textarea class="materialize-textarea" id="responsiveSize" name="content" rows="5" style="color:black font-size:24px" required></textarea><br/>
    <button id="backButton text-center" style = "top: 0px; left: 0px; position:relative" onclick="goBack()" type="button" class="btn btn-primary btn-raised">
        <i aria-hidden="true"></i><span>Back</span>
    </button>
    <button type="submit"  class="btn  btn-four initialism slide_open" style="color:black" value="Submit Response">Submit Response</button><br/><br/>

  </form>
</div>


<script>
function loadModal(comment_id, discussion_id){
    document.getElementById('passCommentID').value=comment_id ;
    document.getElementById('passDiscussionID').value=discussion_id ;
}
</script>

@endsection