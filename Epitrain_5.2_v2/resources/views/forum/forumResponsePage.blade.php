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



	 ?></font> <br/>
</div>



@endforeach
<button id="backButton text-center" style = "top: 0px; left: 0px; position:relative" onclick="goBack()" type="button" class="btn btn-primary btn-raised">
    <i aria-hidden="true"></i><span>Back</span>
</button>





@endsection