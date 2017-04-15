@extends('layouts.app')

@section('content')

<div class="container">

	<div class="row">
	<div class="col-sm-5">
	<h1>Frequently Asked Questions</h1>
	</div>
	
	
	@if (Auth::user()->isAdmin)
		<div class="col-sm-7">
		<form action=<?php echo url('faq/create');?> method="get" >
				<button id="" class="btn btn-raised btn-primary initialism basic_open" >
								Add A Question
			</button>
		</form>
		</div>
	@endif
	</div>
	<br>
	<br>
	<?php
		$faqs1 = \DB::table('faq')
						->where('category', 'basic')
						->orderBy('created_at', 'desc')
						->get();
		$faqs2 = \DB::table('faq')
						->where('category', 'advance')
						->orderBy('created_at', 'desc')
						->get();


	?>
	<div class="row">
	<div class="list-group col-sm-12 col-lg-10">
	<table>
		@foreach($faqs1 as $faq)
		
			

			<tr height="100">
				
					<td ><p align="left"><span class="badge badge-primary" >Q</span>&nbsp&nbsp<?php echo $faq->question?></p></td>
					<td ><p align="left"><span class="badge badge-default" >A</span>&nbsp&nbsp<?php echo $faq->answer?></p></td>
				
				
				@if (Auth::user()->isAdmin)
					<td >
					<div class="col-sm-2">	
						<form action=<?php echo url('faq/delete');?> method="get" >
							<input type="hidden" name="id" value=<?php echo $faq->id?>>
								<button  id="" class="btn btn-default btn-raised" style="background-color:white">
											<i class="fa fa-times" aria-hidden="true"></i>
								</button>
						</form>
						<form action=<?php echo url('faq/edit');?> method="get" >
							<input type="hidden" value=<?php echo $faq->id?> name="id">
								<button  id="" class="btn btn-default btn-raised" style="background-color:white">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								</button>
						</form>
					</div>
					<br>
					<br>
					</td>
				@endif
			</tr>
			
		@endforeach

		@foreach($faqs2 as $faq)
		
			

			<tr height="100">
				
					<td ><p align="left"><span class="badge badge-primary" >Q</span>&nbsp&nbsp<?php echo $faq->question?></p></td>
					<td ><p align="left"><span class="badge badge-default" >A</span>&nbsp&nbsp<?php echo $faq->answer?></p></td>
				
				
				@if (Auth::user()->isAdmin)
					<td >
					<div class="col-sm-2">	
						<form action=<?php echo url('faq/delete');?> method="get" >
							<input type="hidden" name="id" value=<?php echo $faq->id?>>
								<button  id="" class="btn btn-default btn-raised" style="background-color:white">
											<i class="fa fa-times" aria-hidden="true"></i>
								</button>
						</form>
						<form action=<?php echo url('faq/edit');?> method="get" >
							<input type="hidden" value=<?php echo $faq->id?> name="id">
								<button  id="" class="btn btn-default btn-raised" style="background-color:white">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								</button>
						</form>
					</div>
					<br>
					<br>
					</td>
				@endif
			</tr>
			
		@endforeach
	</table>
	</div>
	</div>


</div>
@endsection