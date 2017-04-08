@extends('layouts.app')
@section('content')

<?php
    $subscriptionPlans = \DB::table('subscriptionplan')
    ->get();

?>
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Manage Subscription</li>
        </ul>
    </div>
</div>
 <div class="container" style="positon:relatvie;top:300px">
 	
	 <div id="page-wrapper" style="margin:20px">
	 	<div class="row">
		<div class="col-md-7">
				<div class="panel panel-primary">
	                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i>Manage Subscription Plans

                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Period length in months</th>
                                                    <th class="text-center">Price</th>
                                                    <th class="text-center">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	@foreach($subscriptionPlans as $plan)
                                            		<tr>
                                            			<td><?php echo $plan->monthperiod;?></td>
                                            			<td>$<?php echo $plan->price;?></td>
                                            			<td>
                                            				<form action=<?php echo url('deleteSubscriptionPlan');?> method="post" >
			                                                  <input type="hidden" name="id" value=<?php echo $plan->id;?>>
			                                                  <button  class="btn btn-info btn-raised btn-sm">
			                                                    <i class="fa fa-window-close" aria-hidden="true"></i>
			                                                  </button>
			                                                </form>
                                            			</td>
                                            		</tr>
                                            	@endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                        <!--<button class="btn btn-success btn-raised">Add a plan</button>-->
                    </div>
                    <!--End simple table example -->
         	</div>

         	<div class="col-md-5">
				<div class="panel panel-primary">
	                   <div class="panel-heading">
                        Add a Plan
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <form action=<?php echo url('addSubscriptionPlan');?> method="post" >
			                                  Period length in months:<br>
											  <input type="text" name="monthperiod">
											  <br>
											  Price:<br>
											  <input type="text" name="price">
											  <br><br>
											  <input type="submit" class="btn btn-success btn-raised" value="Submit">
			                             </form>
                                    </div>

                                </div>

                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!--End simple table example -->
         	    </div>
            </div>
       </div>
  </div>


@endsection