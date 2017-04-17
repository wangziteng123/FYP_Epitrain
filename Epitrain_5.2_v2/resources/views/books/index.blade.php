@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Search Result</li>
        </ul>
    </div>
</div>

<style>
body{
	color:black;
}
</style>
<?php
   use Carbon\Carbon;
   use Illuminate\Notifications\Notifiable;
   use App\Notifications\SubscriptionExpiring;
   
   $isSubscribe = Auth::user()->subscribe;
 ?>

@if($isSubscribe)
  <?php
    $currentTime = Carbon::now();
    $userSubscribe = \DB::table('subscription')
      ->where('user_id', Auth::user()->id)
      ->orderBy('id','desc')
      ->first();

    $end_Date = Carbon::createFromFormat('Y-m-d H:i:s', $userSubscribe->end_date);
    
    $expiring = $currentTime->diffInHours($end_Date);
    
    $informedEnding = $userSubscribe->informed_ending;
    $homeUrl = URL::route('home');
    
    $user = Auth::user();
    if($expiring <= 168){
        if($informedEnding == 0){                    
            $user->notify(new SubscriptionExpiring($homeUrl));
            DB::table('subscription')
                -> where ('user_id', '=', $user->id)
                -> update(['informed_ending' => 1]);
        }
    } elseif($expiring > 168 && $userSubscribe->informed_ending == 1){
        DB::table('subscription')
            -> where ('user_id', '=', $user->id)
            -> update(['informed_ending' => 0]);
    }
    
    $notYetExpired = $currentTime->lt($end_Date);
?>
@endif
<?php
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $parts = parse_url($url);
    parse_str($parts['query'], $chosenCategory);
    //var_dump($parts);
    if (strcmp($chosenCategory['cat'], "viewAll") == 0) {
      $bookList = \DB::table('fileentries')
        ->paginate(10)
        ->setPath('/shop?cat='.$chosenCategory['cat']);
    } else {
      $bookList = \DB::table('fileentries')
        ->where('category', $chosenCategory['cat'])
        ->paginate(10)
        ->setPath('/shop?cat='.$chosenCategory['cat']);
    }
    //var_dump($bookList);
?>
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">Epishop</div>

                <div class="panel-body">
                  <?php if (strcmp($chosenCategory['cat'], "viewAll") == 0) { ?>
                    <h2><font color="black">All books in shop</font></h2>
                  <?php } else { ?>
                    <h2><font color="black">Books under <?php echo $chosenCategory['cat'];?> </font></h2>
                  <?php };?>
                </div>

                @foreach($bookList as $book)
                    <div class="col-sm-12 col-xs-12" style="position:relative">
                    <br/>
                    <?php
                        $checkid = $book->id;
                        $checkidStr = (string) $checkid;
                        $checkidStr = ",". $checkidStr;

                        $countNum = 0;
                        $countNum ++;
                        //array_push($filenameArr,$book->filename);
                        $container = "container".$countNum;

                        $shoppingcartExist = \DB::table('shoppingcarts')
                            ->where('user_id', Auth::user()->id)
                            ->where('fileentry_id', $checkid)
                            ->get();

                        $libraryExist = \DB::table('libraries')
                            ->where('user_id', Auth::user()->id)
                            ->where('fileentry_id', $checkid)
                            ->get();

                        $coursesOfThisBook = \DB::table('courseMaterial')
                          ->join('course', function ($join) use ($checkid) {
                              $join->on('course.courseID', '=', 'courseMaterial.courseID')
                                   ->where('courseMaterial.fileEntriesID', '=', $checkid)
                                   ->where('course.isActive','=','1');
                              })
                              ->distinct()
                              ->pluck('courseMaterial.courseID');

                        $coursesOfThisUser = \DB::table('enrolment')
                        ->where('userID', Auth::user()->id)
                        ->where('isActive','=',1)
                        ->pluck('courseID');

                        $isStudent = false;
                        foreach ($coursesOfThisBook as $course) {
                            if(in_array($course, $coursesOfThisUser)) {
                               $isStudent = true;
                               break;
                            }
                        }
												$hasSample = false;
												if($book->sample_id != null){
													$hasSample = true;
												}

                    ?>
                      @if($isSubscribe || $isStudent)
                        @if($isSubscribe)
                          <!-- user is a subscriber -->
                          @if($notYetExpired)
                            <div class="jumbotron" style="background:#E1DFDE">
                              <div class = "row">                                 
                                  <div class="col-md-6 col-sm-10 col-xs-10 col-xs-offset-1 col-md-offset-3 text-xs-center"><font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $book->original_filename;?></font></div>
                              </div>
                              <div class = "row">
                                  <div class="col-md-6 col-sm-9 col-xs-9 col-md-offset-3 col-xs-offset-1 col-sm-offset-1"><font color="black" size='4'><strong>Category:</strong>  <?php echo $book->category;?></font></div>
                                  <div class="col-sm-6 col-sm-offset-3 hidden-xs"><font color="black" size='4'><strong>Description:</strong>  <?php 
                                    if (strlen($book->description) == 0) {
                                        echo "No description";
                                    } else {
                                        echo $book->description;
                                    } 
                                  ?></font></div>       
                              </div>
                              <div class = "row center-block">
                                <div class="col-sm-4 col-xs-4 col-xs-offset-4">
                                    @if (count($libraryExist))
                                      <button class="btn btn-warning" style="background-color: darkblue; color:yellow">
                                          <font style="">Already purchased</font>
                                      </button>
                                    @else
                                    <form action=<?php echo URL::route('addToLibraryOne');?> method="post">
                                        <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                        <input type="hidden" name="fid" value=<?php echo $checkid;?>>
                                        <button class="btn btn-raised btn-warning">
                                            <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                                            Add To Library
                                        </button>
                                     </form>

                                    @endif
                                </div>
                              </div>
                            </div>
                          @else
                            <div class="jumbotron" style="background:#E1DFDE">
                              <div class = "row">
                                  
                                  <div class="col-md-6 col-sm-10 col-xs-10 col-xs-offset-1 col-md-offset-3 text-xs-center"><font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $book->original_filename;?></font></div>
                                  
                                  <div class="col-md-1 hidden-xs hidden-sm col-md-offset-1"><font color="black" style="font-size:28px">S$<?php echo $book->price;?></font>    </div>
                                
                              </div>
                              <div class = "row">
                                  <div class="col-md-6 col-sm-9 col-xs-9 col-md-offset-3 col-xs-offset-1 col-sm-offset-1"><font color="black" size='4'><strong>Category:</strong>  <?php echo $book->category;?></font></div>
                                  <div class="col-sm-6 col-sm-offset-3 hidden-xs"><font color="black" size='4'><strong>Description:</strong>  <?php 
                                    if (strlen($book->description) == 0) {
                                        echo "No description";
                                    } else {
                                        echo $book->description;
                                    } 
                                  ?></font></div>       
                              </div>
                              <div class = "row center-block">
                                  <div class="col-xs-12 col-sm-12 visible-xs visible-sm center-block"><font color="black" style="font-size:28px">S$<?php echo $book->price;?></font>    
                                  </div>
                              </div>
                              <div class = "row center-block">
                                <div class="col-sm-3 col-xs-3 col-xs-offset-2">
                                  @if (count($shoppingcartExist))
                                      <button class="btn btn-warning" style="background-color: darkblue; color:yellow">
                                          <font>Already Added</font>
                                      </button>
                                  @else
                                      <form action=<?php echo url('shoppingcart/add');?> method="post">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $checkid;?>>
                                              <button  class="btn btn-raised btn-info">
                                                  <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                                  Add to Cart 
                                              </button>
                                       </form>
                                  @endif
                                </div>
																
																@if($hasSample)
																	<div class="col-sm-2 col-xs-3 hidden-xs">
																			<a href="{{route('getsampleviewer', $book->sample_id)}}" class="btn-raised btn-info btn">View Sample</a> 
																		
																	</div>
																@endif
																
                                <div class="col-sm-3 col-xs-3 hidden-xs">
                                  @if (count($libraryExist))
                                    <button class="btn btn-warning" style="background-color: darkblue; color:yellow">
                                        <font style="">Already purchased</font>
                                    </button>
                                  @else
                                  <form action=<?php echo URL::route('payment');?> method="post">
                                      <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                      <input type="hidden" name="fidStr" value=<?php echo $checkidStr;?>>
                                      <input type="hidden" name="totalPrice" id="totalPrice" value="<?php echo $book->price;?>"/>
                                          <button class="btn btn-raised btn-warning">
                                              <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                                              Buy Now
                                          </button>
                                   </form>

                                  @endif
                                </div>
                              </div>
                            </div>
                          @endif
                        @else
                          <!-- user is a student -->
                          <div class="jumbotron" style="background:#E1DFDE">
                              <div class = "row">                                 
                                  <div class="col-md-6 col-sm-10 col-xs-10 col-xs-offset-1 col-md-offset-3 text-xs-center"><font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $book->original_filename;?></font></div>
                              </div>
                              <div class = "row">
                                  <div class="col-md-6 col-sm-9 col-xs-9 col-md-offset-3 col-xs-offset-1 col-sm-offset-1"><font color="black" size='4'><strong>Category:</strong>  <?php echo $book->category;?></font></div>
                                  <div class="col-sm-6 col-sm-offset-3 hidden-xs"><font color="black" size='4'><strong>Description:</strong>  <?php 
                                    if (strlen($book->description) == 0) {
                                        echo "No description";
                                    } else {
                                        echo $book->description;
                                    } 
                                  ?></font></div>       
                              </div>
                              <div class = "row center-block">
                                <div class="col-sm-4 col-xs-4 col-xs-offset-4">
                                    @if (count($libraryExist))
                                      <button class="btn btn-warning" style="background-color: darkblue; color:yellow">
                                          <font style="">Already purchased</font>
                                      </button>
                                    @else

                                    <form action=<?php echo URL::route('addToLibraryOne');?> method="post">
                                        <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                        <input type="hidden" name="fid" value=<?php echo $checkid;?>>
                                        <button class="btn btn-raised btn-warning">
                                            <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                                            Add To Library
                                        </button>
                                     </form>

                                    @endif
                                </div>
                              </div>
                            </div>
                        @endif
                      @else 
                        <div class="jumbotron" style="background:#E1DFDE">
                          <div class = "row">
                              
                              <div class="col-md-6 col-sm-10 col-xs-10 col-xs-offset-1 col-md-offset-3 text-xs-center"><font color="darkblue" style="font-size: 25px;font-weight: bold;"><?php echo $book->original_filename;?></font></div>
                              
                              <div class="col-md-1 hidden-xs hidden-sm col-md-offset-1"><font color="black" style="font-size:28px">S$<?php echo $book->price;?></font>    </div>
                            
                          </div>
                          <div class = "row">
                              <div class="col-md-6 col-sm-9 col-xs-9 col-md-offset-3 col-xs-offset-1 col-sm-offset-1"><font color="black" size='4'><strong>Category:</strong>  <?php echo $book->category;?></font></div>
                              <div class="col-sm-6 col-sm-offset-3 hidden-xs"><font color="black" size='4'><strong>Description:</strong>  <?php 
                                if (strlen($book->description) == 0) {
                                    echo "No description";
                                } else {
                                    echo $book->description;
                                } 
                              ?></font></div>       
                          </div>
                          <div class = "row center-block">
                              <div class="col-xs-12 col-sm-12 visible-xs visible-sm center-block"><font color="black" style="font-size:28px">S$<?php echo $book->price;?></font>    
                              </div>
                          </div>
                          <div class = "row center-block">
                            <div class="col-sm-3 col-xs-3 col-xs-offset-2">
                              @if (count($shoppingcartExist))
                                  <button class="btn btn-warning" style="background-color: darkblue; color:yellow">
                                      <font>Already Added</font>
                                  </button>
                              @else
                                  <form action=<?php echo url('shoppingcart/add');?> method="post">
                                      <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                      <input type="hidden" name="fid" value=<?php echo $checkid;?>>
                                          <button  class="btn btn-raised btn-info">
                                              <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                              Add to Cart
                                          </button>
                                   </form>
                              @endif
                            </div>
														@if($hasSample)
																	<div class="col-sm-2 col-xs-3">
																			<a href="{{route('getsampleviewer', $book->sample_id)}}" class="btn-raised btn-info btn">View Sample</a> 
																		
																	</div>
																@endif
														
                            <div class="col-sm-3 col-xs-3 hidden-xs">
                              @if (count($libraryExist))
                                <button class="btn btn-warning" style="background-color: darkblue; color:yellow">
                                    <font style="">Already purchased</font>
                                </button>
                              @else
                              <form action=<?php echo URL::route('payment');?> method="post">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fidStr" value=<?php echo $checkidStr;?>>
                                  <input type="hidden" name="totalPrice" id="totalPrice" value="<?php echo $book->price;?>"/>
                                      <button class="btn btn-raised btn-warning">
                                          <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                                          Buy Now
                                      </button>
                               </form>

                              @endif
                            </div>
                          </div>
                        </div>
                      @endif
                    </div>
                @endforeach

            </div>
        </div>
        {{ $bookList->links() }}
    </div>
</div>
<link rel="stylesheet" href="{{ URL::asset('css/style.css') }}" />
@endsection



