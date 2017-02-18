@extends('layouts.app')

@section('content')

<?php 
	$discussion = \DB::table('forumdiscussion') 
		->where('id', $discussionId)
		->get();

	$responses = \DB::table('forumresponse') 
		->where('discussion_id', $discussionId)
		->get();

?>



@foreach($discussion as $disc)
<div class="jumbotron" style='margin-left: 50px; margin-right: 50px'>
	<font color='black'><h2><b><?php echo $disc->title;?></b></h2></font> <br/>
	<font color='black'><?php echo $disc->description;?></font> <br/>
</div>
@endforeach

@foreach($responses as $response)
<div class="jumbotron responsiveSize" >
	<font color='black'><?php echo $response->content;?> <br/>


	<?php

    	$userID = $response->user_id;
    	$userName = "";
        // getting the username from the userID
        if (Auth::check()) {
            $user_record = DB::table('users')->where('id', $userID)->first();
            $userName =  $user_record->name;

        }
        echo "Posted by " . $userName;
        //echo $userName; ?> </font>

        <font color='grey'>

    <?php

	$creationDate = $response->created_at;

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





	    ?></font> <br/>
</div>
@endforeach

<div class="responsiveSize">
<form method="post" action=<?php echo URL::route('createResponse');?>>
<input type="hidden" name="discussionId" value=<?php echo $discussionId;?>>
<input type="hidden" name="user_id" value=<?php echo \Auth::user()->id;?>>

<textarea id="responsiveSize" name="content" rows="5" style="color:black" required></textarea><br/>
<button type="submit"  class="btn  btn-four initialism slide_open" style="color:black" value="Submit Response">Submit Response</button><br/><br/>

</form>
</div>





@endsection