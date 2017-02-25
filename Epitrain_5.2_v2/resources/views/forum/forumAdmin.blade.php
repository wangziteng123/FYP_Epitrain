@extends('layouts.app')

@section('content')

<?php
	$categories = \DB::table('forumcategory') ->get();

	$discussions = \DB::table('forumdiscussion')
		->join('forumcategory', 'forumdiscussion.category_id', '=', 'forumcategory.id')
		->join('users', 'forumdiscussion.user_id', '=', 'users.id')
        ->select('forumdiscussion.*', 'forumcategory.categoryname', 'users.name')
        ->get();




?>

@if (session()->has('flash_notification.message'))
       <div class="alert alert-{{ session('flash_notification.level') }}">
           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

           {!! session('flash_notification.message') !!}
       </div>
@endif


<div class="col-lg-12 forumMenu" >
    <div class="col-lg-11 " style="position:relative; left:70px; " >
    <h1 style="position: absolute;left: 14px;">Discussion Forum (Admin)</h1>
    <br/><br/><br/>
    <hr>
        <div class="col-lg-3" style="position:absolute;left:15px;">
    	<button class="btn btn-four btn-lg initialism slide_open"><i class="fa fa-plus-circle" aria-hidden="true"></i>  NEW DISCUSSION</button>
    	<br/><br/>








</br>



<!-- Button trigger modal for adding category -->
<button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#myModal">
     Add Category
</button>

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














    	<!--
    	<a href="#"><font size="3" style="color:white">All Discussion</font></a> -->
    	<br/>
    	<br/>
    	<!--
    	@foreach($categories as $category)
    		<a href="#"><font size="3" style="color:white"><?php echo $category->categoryname?></font></a><br/>
    	@endforeach -->
        </div></div>
</div>
<div class="col-lg-12" >
        <div class="forumDiscussion" style="border-style:solid;border-width:1px;right:20px;background-color:white;">
    	@foreach($discussions as $discussion)

    	<?php
	    	$forumpageUrl = URL::route('forumpage');
	    	$forumpageUrl = $forumpageUrl."?id=".$discussion->id;


            $isOpen= $discussion->isOpen; // needed to see if the discussion is still open for reply

	    	$showResponsePageUrl = URL::route('forumResponsePage');
	    	$showResponsePageUrl= $showResponsePageUrl."?id=".$discussion->id;

	    	$discussionId = $discussion->id;


	    	$responses = \DB::table('forumresponse')
            		->where('discussion_id', $discussionId)
            		->get();

            $numOfResponses = count($responses);
           //echo var_dump($numOfResponses);
	    ?>


	    <div class="jumbotron" >
	        <div class="row">


                <div class="col-sm-8"><font color='black'><h2>
                    <b><?php echo $discussion->title;?></b></h2></font>

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
	    			<font color='black'><?php echo $discussion->description;?></font> <br/>

	    			<?php
                        //check if the discussion has been closed or not
                        if ($isOpen == 0){ ?>
                            <a style="display:block" href=<?php echo $forumpageUrl?>>
                            Reply <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            </font><br/>
                            <!-- this is to close the discussion-->
                            <form method="post" action=<?php echo URL::route('closeDiscussion');?>>
                            <input type="hidden" name="discussionId" value=<?php echo $discussionId;?>>
                            <button type="submit" class="btn btn-warning">Close Discussion</button>
                            </form></br>

                            <?php

                        }else{ ?>
                            <font color='red'> This discussion has been closed </font>

                        <?php
                        }

                        ?>
<!--

	    			<form method="post" id="deleteForm" action=<?php echo URL::route('deleteDiscussion');?> onsubmit="return confirmDelete()">
	    			<input type="hidden" name="discussionId" value=<?php echo $discussionId;?>>
	    			<input type="submit" value="Delete Discussion" class="btn btn-danger"></button>
	    			</form>


-->


</br>
<!-- Button trigger modal for adding category -->
<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModalDeleteDiscussion">
     Delete Discussion
</button>

<!-- Modal for adding category -->
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
            <input type="hidden" name="discussionId" value=<?php echo $discussionId;?>>

        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

        <input type="submit" value="Delete" class="btn btn-primary"></button>

        </div>
        </form></font>


      </div>

    </div>
  </div>
</div>


















	    	    </div>
	    	    <div class="col-sm-4">
                    <font color='black'><a style="text-decoration: none" href=<?php echo $showResponsePageUrl?>><h2>
                    <i class="fa fa-comment-o"></i><?php echo $numOfResponses ?></h2></a> </font>
	    	    </div>
	    	<br/>


	    	</div>
</div>
    	@endforeach
        </div>

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
		<textarea name="description" rows="4" cols="50" required></textarea><br/>
		<input type="submit" value="Submit" style="position:absolute;right:35px;"><br/><br/>
		<button class="btn btn-default slide_close" style="position:absolute;right:30px;width:70px">Cancel</button>
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

function confirmDelete(){
    var result = confirm("Are you sure you want to delete this discussion?");
    console.log(result);
    if (result == false) {
        //window.location.reload();
       // document.getElementById("deleteForm").innerHTML = "";
       return false;
    }

}









</script>


@endsection