@extends('layouts.app')

@section('content')
<?php
$id = $_GET['id'];

$faq = \DB::table('faq')
          ->where('id', $id)
          ->first();

     $question = $faq->question;
     $answer = $faq->answer;
     $category = $faq->category;

?>

<div style="position:relative">

<div style="position:absolute;top:20px;left:100px">
<h1>Add</h1>
</div>
<br/><br/><br/><br/>
<div class="col-lg-9" style="position:absolute;left:80px"><hr></div>

<div style="position:absolute;left:100px;top:110px"><font style="font-size:17px">Question:</font></div>
<div style="position:absolute; left:100px;top:135px">
	<textarea form ="testformid" name="question"  id="taid" cols="65" rows="10" wrap="soft"><?php echo $question?></textarea>
</div>

<div style="position:absolute;left:640px;top:110px"><font style="font-size:17px">Answer:</font></div>
<div style="position:absolute; left:640px;top:135px">
	<textarea form ="testformid" name="answer"  id="taid" cols="65" rows="10" wrap="soft"><?php echo $answer?></textarea>
</div>

@if($category==="basic")
	<div style="position:absolute;left:740px;top:405px">
	<select name="category">
	  <option value="basic" selected>basic</option>
	  <option value="advance">advance</option>
	</select>
	</div>
@else
	<div style="position:absolute;left:740px;top:405px">
	<select name="category">
	  <option value="basic">basic</option>
	  <option value="advance" selected>advance</option>
	</select>
	</div>
@endif


<button  class="btn btn-raised" style="position:absolute;left:900px;top:390px" onclick="goBack()">
    Cancel
</button>

<form action=<?php echo url('faq/editFaq');?> method="post" id="testformid" style="position:absolute;left:1000px;top:390px">
	<input type="hidden" name="id" value=<?php echo $id?>>
    <input type="submit" value="save" class="btn btn-info btn-raised"/>
</form> 


</div>

<script>
	var mainUrl = window.location.hostname;  

	function goBack() {
		//alert("sdfsdfsd");
		window.location.replace("http://" + mainUrl + "/faq");
	}

</script>




@endsection