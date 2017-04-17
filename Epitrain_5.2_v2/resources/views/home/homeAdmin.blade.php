<!-- Page Content -->
<?php
use Carbon\Carbon;

//total revenue
 $revenues = \DB::table('libraries')
  ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
  ->selectRaw('fileentry_id, COUNT(*) as count,price')
  ->groupBy('fileentry_id')
  ->orderBy('count', 'desc')
  ->get();

  $totalRevenue = 0;
  foreach ($revenues as $revenue) {
   	$count = (int)$revenue->count;
   	$price = (double)$revenue->price;

   	$totalRevenue += $count * $price;
  }	

//total users
$users = \DB::table('users')
              ->get();

$numOfUsers = count($users);

//total ebooks
$ebooks = \DB::table('fileentries')
              ->get();

$numOfEbooks = count($ebooks);

//total discussions
$forumdiscussions = \DB::table('forumdiscussion')
              ->get();

$numOfDiscussions = count($forumdiscussions);

$numOfUnreadThreads = 0;
foreach($forumdiscussions as $discussion) {
    if($discussion->views === 0) {
        $numOfUnreadThreads++;
    }
}

//top purchased books
$trendingEbooks = \DB::table('libraries')
          ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
          ->select(DB::raw('count(*) as fileentry_count'),'fileentries.*', 'libraries.fileentry_id')
          ->groupBy('libraries.fileentry_id')
          ->orderBy('fileentry_count', 'desc')
          ->take(10)
          ->get();


//revenue in each month
$dt = Carbon::now();
$dt1 = Carbon::now();
$dt2 = Carbon::now();
$dt3 = Carbon::now();
$dt4 = Carbon::now();
$lastFiveMonthsArr = array($dt, $dt1->subMonth(), $dt2->subMonths(2), $dt3->subMonths(3), $dt4->subMonths(4) );
$finalYearAndMonthArray = array();
$finalRevenueArray = array();

// for( $i = 1; $i < 5; $i++) {
// 	$dt2 = $dt;
// 	$lastfiveMonth = $dt2->subMonths($i); 
// 	array_push($lastFiveMonthsArr,$lastfiveMonth);
// }

// foreach($lastFiveMonthsArr as $monthyear) {
// 	$year = $monthyear->year;
// 	$month = $monthyear->month;
// 	$yearAndMonth = $year.' '.$month;

// 	array_push($finalYearAndMonthArray,$yearAndMonth);
// }

foreach($lastFiveMonthsArr as $monthyear) {
	$year = $monthyear->year;
	$month = $monthyear->month;
	$yearAndMonth = (String)$year.'.'.(String)$month;

	$revenuesInOneMonth = \DB::table('libraries')
	  ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
	  ->whereMonth('libraries.created_at', '=', date($month))
	  ->whereYear('libraries.created_at', '=', date($year))
	  ->selectRaw('fileentry_id, COUNT(*) as count,price')
	  ->groupBy('fileentry_id')
	  ->orderBy('count', 'desc')
	  ->get();

  $totalRevenueInOneMonth = 0;
  foreach ($revenuesInOneMonth as $revenue1) {
   	$count1 = (int)$revenue1->count;
   	$price1 = (double)$revenue1->price;

   	$totalRevenueInOneMonth += $count1 * $price1;
  }	

  	$finalRevenueArray[$yearAndMonth] = $totalRevenueInOneMonth;

}



?>
  
        <!--  page-wrapper -->
        <div id="page-wrapper" style="margin:20px">

            <div class="row">
                <!-- Page Header -->
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!--End Page Header -->
            </div>

            <div class="row">
                <!-- Welcome -->
                <div class="col-lg-12">
                    <div class="alert alert-info" style="background-color:#01466f; color:white">
                        <i class="fa fa-folder-open"></i><b>&nbsp;Hello ! </b>Welcome Back <b>{{ Auth::user()->name }}</b>
                    </div>
                </div>
                <!--end  Welcome -->
            </div>


            <div class="row">
                <!--quick info section -->
                <div class="col-lg-3">
                    <div class="alert alert-danger text-center"  style="background-color: green;color:white">
                        <i class="fa fa-usd fa-3x"></i>&nbsp;&nbsp;<b><?php echo $totalRevenue?>$</b> total revenue made
                        <br/><br/>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="alert alert-success text-center"style="color:white">
                        <i class="fa fa-users fa-3x"></i>&nbsp;&nbsp;<b><?php echo $numOfUsers?> </b>total users
                        <br/><br/><a href="{{ url('/viewAllUsers') }}" class="btn btn-default btn-raised">Manage</a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="alert alert-info">
                        <i class="fa fa-book fa-3x"></i>&nbsp;&nbsp;<b><?php echo $numOfEbooks?> </b> ebooks in total
                        <br/><br/><a href="{{ url('/fileentry') }}" class="btn btn-default btn-raised">Manage</a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="alert alert-warning text-center">
                        <i class="fa fa-comments fa-3x"></i>&nbsp;&nbsp;<b><?php echo $numOfDiscussions?> </b> discussions in forum
                        <br/>
                        <b>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $numOfUnreadThreads?> </b> unread discussions
                        <br/><a href="{{ url('/forumAdmin') }}" class="btn btn-default btn-raised">Manage</a>
                    </div>
                </div>                
                <!--end quick info section -->
            </div>

            <div class="row">
                <div class="col-md-8">



                    <!--Area chart example -->
                    <div class="panel panel-primary">
											
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i>Revenue trend
                        </div>

                        <div class="panel-body">
							<div class="container col-sm-12 col-xs-12">
                            <div id="line-example"></div>
							</div>
                        </div>
											
                    </div>
                    <!--End area chart example -->
                    <!--Simple table example -->
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i>Revenue in each month

                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th>Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	@foreach($finalRevenueArray as $yearAndMonth=>$revenue)
                                                <tr>                                                    
                                                    <td><?php echo $yearAndMonth;?></td>
                                                    <td><?php echo $revenue;?></td>
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
                    </div>
                    <!--End simple table example -->

                </div>

                <div class="col-lg-4">
                    <!-- <div class="panel panel-primary text-center no-boder">
                        <div class="panel-body yellow">
                            <i class="fa fa-odnoklassniki fa-3x"></i>
                            <h3>20,741 </h3>
                        </div>
                        <div class="panel-footer">
                            <span class="panel-eyecandy-title">Daily User Visits
                            </span>
                        </div>
                    </div> -->
                    <!--Simple table example -->
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i>Top Purchased Books

                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	@foreach($trendingEbooks as $trendingEbook)
                                            	<?php
                                            		$name = $trendingEbook->original_filename;
                                            		$count = (int)$trendingEbook->fileentry_count;
                                            		$price = (double)$trendingEbook->price;
                                            	?>
                                                <tr>
                                                    <td><?php echo $name;?></td>
                                                    <td><?php echo $count;?></td>
                                                    <td><?php echo $count*$price;?></td>
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
                    </div>
                    <!--End simple table example -->


                </div>

            </div>

         


        </div>
        <!-- end page-wrapper -->


 		  <div id="slide" class="well" style="position:relative;top:30px;width:600px;height:400px">
              <button class="slide_close btn btn-default" style="position:absolute;right:20px"><i class="fa fa-times" aria-hidden="true"></i></button>
              <br/><br/>
              <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>10/21/2013</td>
                                                    <td>3:29 PM</td>
                                                    <td>$321.33</td>
                                                </tr>
                                                <tr>
                                                    <td>10/21/2013</td>
                                                    <td>3:20 PM</td>
                                                    <td>$234.34</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
             
          </div>
  
		 <script>
			$(document).ready(function () {

			    $('#slide').popup({
			        focusdelay: 400,
			        outline: true,
			        vertical: 'top'
			    });

			    $('#basic').popup();
			});
		</script>


		<script>
			Morris.Line({
			  element: 'line-example',
			  data: [
			  	<?php foreach(array_reverse($finalRevenueArray) as $yearAndMonth=>$revenue) {?>
			  		{ yearmonth: <?php echo $yearAndMonth?>, a: <?php echo $revenue?>  },
			  		//{ yearmonth: "2018.3", a: 29 },
			  	<?php } ?>
			    // { yearmonth: '2017.4', a: 100 },
			    // { yearmonth: '2017.5', a: 75 },
			    // { yearmonth: '2017.6', a: 50},
			    // { yearmonth: '2017.7', a: 75 },
			    // { yearmonth: '2017.8', a: 50},
			  ],

			  xkey: 'yearmonth',
			  ykeys: ['a'],
			  labels: ['Revenue'],
			  parseTime:false,
	          hideHover:true,
	          lineWidth:'6px',
	          stacked: true      
			});

		</script>