@extends('layouts.app')

@section('content')

<div style="position:relative">

<div style="position:absolute;top:20px;left:100px">
<h1>Frequently Asked Questions</h1>
</div>

@if (Auth::user()->isAdmin)
	<form action=<?php echo url('faq/create');?> method="get" style="position:absolute;width:150px;top:50px;right:370px">
	    <button id="" class="btn btn-raised btn-primary initialism basic_open" >
	            Add A Question
		</button>
	</form>
@endif

<?php
	$faqs = \DB::table('faq')
          ->orderBy('created_at', 'desc')
          ->get();


?>

<div class="list-group col-lg-8" style="position:absolute;top:120px;left:100px">
@foreach($faqs as $faq)
  <div class="list-group-item list-group-item-action flex-column align-items-start" style="height:auto;min-height:70px;">

  	@if (Auth::user()->isAdmin)
	  	<form action=<?php echo url('faq/delete');?> method="get" style="float:right">
	  		<input type="hidden" name="id" value=<?php echo $faq->id?>>
		    <button  id="" class="btn" style="background-color:white">
		            <i class="fa fa-times" aria-hidden="true"></i>
			</button>
		</form>
	  	<form action=<?php echo url('faq/edit');?> method="get" style="float:right">
	  		<input type="hidden" value=<?php echo $faq->id?> name="id">
		    <button  id="" class="btn" style="background-color:white">
		            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
			</button>
		</form>
	@endif

    <span class="badge badge-primary" style="float:left">Q</span><font style="float:left">&nbsp&nbsp<?php echo $faq->question?></font>
    <br>
    <span class="badge badge-default" style="float:left">A</span><font style="float:left">&nbsp&nbsp<?php echo $faq->answer?></font>
  </div>
@endforeach
</div>


</div>
@endsection