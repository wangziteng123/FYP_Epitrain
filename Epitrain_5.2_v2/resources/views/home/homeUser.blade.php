   <!-- Page Content -->
    <div class="container" style="positon:relative">
        <div class="row">
          <h2>Epitrain provides training, resource development & consultancy</h2>
        </div>
          <br/>
          <br/>
        <div style="position:absolute;left:118px;;top:215px">
          <hr style="width:105px">
        </div>

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
        @if($notYetExpired)
          <div class="row">
          Your subscription plan will end at <?php echo $end_Date->toDateTimeString();?>.
          </div>
        @else
        <div class="row">
         Want to start a subscription? &nbsp&nbsp
        <button  class="btn btn-raised btn-primary initialism basic_open" style="width:150px;">
          SUBSCRIBE
        </button>
       </div>

        @endif
      @else
        <div class="row">
         Want to start a subscription? &nbsp&nbsp
        <button  class="btn btn-raised btn-primary initialism basic_open" style="width:150px;">
          SUBSCRIBE
        </button>
       </div>

      @endif

      <div class="row">
        <div class="col-sm-2">
        <font style="font-family:Book Antiqua;font-weight:10" size="6">Trending </font>
        <hr>
        </div>
      </div><br/>
      
        
        <?php

          $trendingEbooks = \DB::table('libraries')
          ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
          ->select(DB::raw('count(*) as fileentry_count'),'fileentries.*', 'libraries.fileentry_id')
          ->groupBy('libraries.fileentry_id')
          ->orderBy('fileentry_count', 'desc')
          ->take(4)
          ->get();


          // $trendingEbooks = \DB::table('fileentries')
          // ->take(8)
          // ->get();


          $countTrendingEbooks = count($trendingEbooks); 
          if($countTrendingEbooks<=2) {
              $trendingEbooks = \DB::table('fileentries')
              ->take(4)
              ->get();
          }

        ?>

      <div class="row">
        <div class="col-sm-10">

        <?php
            $countNum2 = 0;
            $filenameArr2 = array();
        ?>
        @foreach($trendingEbooks as $ebook)
         <?php
          $countNum2++;

          $filename2 = $ebook->filename;
          array_push($filenameArr2,$filename2);
          $container2 = "2container".$countNum2;

          $price2 = $ebook->price; 
          $fid2 = $ebook->id;
          $fid2Str = (string) $fid2; //convert to string so that i can pass to payment side when user purchase book
          $fid2Str = ",".$fid2Str;
          
          $oriFilename2 = $ebook->original_filename;
          $description2= $ebook ->description;

          //whether the book is in shoppingcart or not / library
          $shoppingcartExist = \DB::table('shoppingcarts')
          ->where('user_id', Auth::user()->id)
          ->where('fileentry_id', $fid2)
          ->get();

          $libraryExist = \DB::table('libraries')
          ->where('user_id', Auth::user()->id)
          ->where('fileentry_id', $fid2)
          ->get();

          $container2 = "2container".$countNum2;
          
          // check if this user is a student in a course that requires this book
          $coursesOfThisBook = \DB::table('courseMaterial')
            ->join('course', function ($join) use ($ebook) {
                $join->on('course.courseID', '=', 'courseMaterial.courseID')
                     ->where('courseMaterial.fileEntriesID', '=', $ebook->id)
                     ->where('course.isActive','=','1');
                })
                ->distinct()
                ->pluck('courseMaterial.courseID');

          $coursesOfThisUser = \DB::table('enrolment')
          ->where('userID', Auth::user()->id)
          ->where('isActive','=',1)
          ->pluck('courseID');

          $isStudent = false;
          $hasSample = false;
          if(isset($ebook->sample_id)) {
            $hasSample = true;
          }
          foreach ($coursesOfThisBook as $course) {
              if(in_array($course, $coursesOfThisUser)) {
                 $isStudent = true;
                 break;
              }
          }

          ?>                      

           <div class="col-sm-6 col-md-3">
            <div class="thumbnail" style="height:365px;width:210px">
              <div id=<?php echo $container2;?> style="position:relative;height:200px;width:135px;margin: 0 auto;"></div>
                               
                <div class="caption" style="position:relative">
                    <div style="position:relative;margin: 0 auto;">
                        @if(strlen($oriFilename2) > 25)
                        <p>{{substr($oriFilename2,0,25)."..." }}</p>
                        @else
                        <p>{{$oriFilename2}}</p>
                        @endif
                    <font size="1">Category: <?php echo $ebook->category;?></font>
                   </div>

                    @if($isSubscribe || $isStudent)
                      <!-- if user is a subscriber -->     
                      @if($isSubscribe)
                        @if($notYetExpired)

                           @if (count($libraryExist))
                                <button class="btn btn-info btn-raised btn-sm" style="position:absolute;right:92px;top:89px">
                                  Added
                                </button>
                           @else
                              <form action=<?php echo url('shoppingcart/addToLibraryOne');?> method="post" style="position:absolute;right:102px;top:89px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid2;?>>
                                <button class="btn btn-info btn-raised btn-sm">
                                  Add
                                </button>
                              </form>
                           @endif
                        @else
                           <p style="position:absolute;top:90px;left:7px"><font style="font-size:25px;color:#34495E">$<?php echo number_format($price2, 2, '.', '') ?></font></p>

                          @if (count($libraryExist))
                              <form style="position:absolute;right:85px;top:91px;border:none">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fid" value=<?php echo $fid2?>>
                                  <button type="submit" style="border:none;background-color: Transparent">
                                     <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already purchased"></i>
                                  </button>
                              </form>
                              <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:105px"></i>
                          @else
                              <form action=<?php echo URL::route('payment');?> method="post" style="position:absolute;right:85px;top:91px;border:none">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fidStr" value=<?php echo $fid2Str?>>
                                  <input type="hidden" name="totalPrice" id="totalPrice" value=<?php echo $price2 ?> />
                                  <button type="submit" style="border:none;background-color: Transparent">
                                     <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                  </button>
                              </form>
                          @endif


                          @if (count($shoppingcartExist))
                              <form style="position:absolute;right:86px;top:115px">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fid" value=<?php echo $fid2?>>
                                  <button type="submit" style="border:none;background-color: Transparent">
                                     <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already added"></i>
                                  </button>
                              </form>
                              <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:127px"></i>
                          @else
                               <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:115px">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fid" value=<?php echo $fid2;?>>
                                  <button type="submit" style="border:none;background-color: Transparent">
                                     <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                  </button>
                              </form>                
                          @endif
                        @endif
                      <!-- if user is a student -->     
                      @else
                         @if (count($libraryExist))
                              <button class="btn btn-info btn-raised btn-sm" style="position:absolute;right:92px;top:89px">
                                Added
                              </button>
                         @else
                        <form action=<?php echo url('shoppingcart/addToLibraryOne');?> method="post" style="position:absolute;right:102px;top:89px">
                              <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                              <input type="hidden" name="fid" value=<?php echo $fid2;?>>
                              <button class="btn btn-info btn-raised btn-sm">
                                Add
                              </button>
                            </form>
                         @endif
                      @endif
                    <!-- if user is a normal user -->     
                    @else
                         <p style="position:absolute;top:90px;left:7px"><font style="font-size:25px;color:#34495E">$<?php echo number_format($price2, 2, '.', '') ;?></font></p>

                    @if (count($libraryExist))
                        <form style="position:absolute;right:85px;top:91px;border:none">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fid" value=<?php echo $fid2;?>>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already purchased"></i>
                            </button>
                        </form>
                        <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:105px"></i>
                    @else
                        <form action=<?php echo URL::route('payment');?> method="post" style="position:absolute;right:85px;top:91px;border:none">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fidStr" value=<?php echo $fid2Str?>>
                            <input type="hidden" name="totalPrice" id="totalPrice" value= <?php echo $price2 ?> />
                            <button type="submit" style="border:none;background-color: Transparent">
                               <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                            </button>
                        </form>
                    @endif


                    @if (count($shoppingcartExist))
                        <form style="position:absolute;right:86px;top:115px">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fid" value=<?php echo $fid2?>>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already added"></i>
                            </button>
                        </form>
                        <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:127px"></i>
                    @else
                         <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:115px">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fid" value=<?php echo $fid2;?>>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                            </button>
                        </form>                
                    @endif


                    @endif
										@if($hasSample)
										<div style="position:absolute;right:-3px;top:108px">
                                   <button type="submit" style="border:none;background-color: Transparent">
                        <a href="{{route('getsampleviewer', $ebook->sample_id)}}" class="btn-raised btn-xs btn-info btn">Sample</a> 
                        </button>
                    </div>
                    <p style="position:absolute;right:12px;top:76px">
                        <button class="btn btn-info btn-raised btn-xs slide_open"  onclick="passtoSlide(<?php echo $fid2;?>,'<?php echo $oriFilename2;?>',<?php echo $price2;?>,'<?php echo $description2;?>')">
                        Info
                        </button>
                    </p>
                    @endif
                    
                    @if(!$hasSample)
                        <p style="position:absolute;right:12px;top:89px">
                            <button class="btn btn-info btn-raised btn-sm slide_open"  onclick="passtoSlide(<?php echo $fid2;?>,'<?php echo $oriFilename2;?>',<?php echo $price2;?>,'<?php echo $description2;?>')">
                                Info
                            </button>
                        </p>
                    @endif
                      
                    
                  
                </div>
            </div>
          </div>

        @endforeach
      </div>
    </div>



    <!--Best Sellers in Financial Market-->
<br/>
        <div class="row">
          <div class="col-sm-4">
        <font style="font-family:Book Antiqua;font-weight:10" size="6">Financial Market</font>
        <hr style="">
          </div>
        </div><br/>
        
        
        <?php

          $financialEbooks = \DB::table('libraries')
          ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
          ->where('category', 'FinancialMarket')
          ->select(DB::raw('count(*) as fileentry_count'),'fileentries.*', 'libraries.fileentry_id')
          ->groupBy('libraries.fileentry_id')
          ->orderBy('fileentry_count', 'desc')
          ->take(4)
          ->get();


          // $financialEbooks = \DB::table('fileentries')
          // ->take(8)
          // ->get();


          $countFinancialEbooks = count($financialEbooks); 
          if($countFinancialEbooks<2) {
              $financialEbooks = \DB::table('fileentries')
              ->take(4)
              ->get();
          }

        ?>

      <div class="row">
        <div class="col-sm-10">

        <?php
            $countNum4 = 0;
            $filenameArr4 = array();
        ?>
        @foreach($financialEbooks as $ebook)
         <?php
          $countNum4++;

          $filename4 = $ebook->filename;
          array_push($filenameArr4,$filename4);
          $container4 = "4container".$countNum4;

          $price4 = $ebook->price; 
          $fid4 = $ebook->id;
          $fid4Str = (string) $fid4; //convert to string so that i can pass to payment side when user purchase book
          $fid4Str = ",".$fid4Str;
          $oriFilename4 = $ebook->original_filename;
          $description4= $ebook ->description;

          //whether the book is in shoppingcart or not / library
          $shoppingcartExist = \DB::table('shoppingcarts')
          ->where('user_id', Auth::user()->id)
          ->where('fileentry_id', $fid4)
          ->get();

          $libraryExist = \DB::table('libraries')
          ->where('user_id', Auth::user()->id)
          ->where('fileentry_id', $fid4)
          ->get();

          $container4 = "4container".$countNum4;
          
          // check if this user is a student in a course that requires this book
          $coursesOfThisBook = \DB::table('courseMaterial')
          ->where('fileEntriesID', $ebook->id)
          ->pluck('courseID');

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
          ?>                      

           <div class="col-sm-6 col-md-3">
            <div class="thumbnail" style="height:365px;width:210px">
              <div id=<?php echo $container4;?> style="position:relative;height:200px;width:135px;margin: 0 auto;"></div>
                               
              <div class="caption" style="position:relative">
                  <div style="position:relative;margin: 0 auto;">
                      @if(strlen($oriFilename4) > 25)
                      <p>{{substr($oriFilename4,0,25)."..." }}</p>
                      @else
                      <p>{{$oriFilename4}}</p>
                      @endif
                  <font size="1">Category: <?php echo $ebook->category;?></font>
                 </div>

                 @if($isSubscribe || $isStudent)
                    <!-- if user is a subscriber -->
                    @if($isSubscribe)
                      @if($notYetExpired)
                        @if (count($libraryExist))
                            <button class="btn btn-info btn-raised btn-sm" style="position:absolute;right:92px;top:89px">
                              Added
                            </button>
                        @else
                          <form action=<?php echo url('shoppingcart/addToLibraryOne');?> method="post" style="position:absolute;right:102px;top:89px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                <button  class="btn btn-info btn-raised btn-sm">
                                  Add
                                </button>
                              </form>
                        @endif
                      @else
                          <p style="position:absolute;top:90px;left:7px"><font style="font-size:25px;color:#34495E">$<?php echo number_format($price4, 2, '.', '');?></font></p>

                          @if (count($libraryExist))
                              <form style="position:absolute;right:85px;top:91px;border:none">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                  <button type="submit" style="border:none;background-color: Transparent">
                                     <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already purchased"></i>
                                  </button>
                              </form>
                              <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:105px"></i>
                          @else
                              <form action=<?php echo URL::route('payment');?> method="post" style="position:absolute;right:85px;top:91px;border:none">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fidStr" value=<?php echo $fid4Str;?>>
                                  <input type="hidden" name="totalPrice" id="totalPrice" value=<?php echo $price4;?> />
                                  <button type="submit" style="border:none;background-color: Transparent">
                                     <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                  </button>
                              </form>
                          @endif


                          @if (count($shoppingcartExist))
                              <form style="position:absolute;right:86px;top:115px">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                  <button type="submit" style="border:none;background-color: Transparent">
                                     <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already added"></i>
                                  </button>
                              </form>
                              <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:137px"></i>
                          @else
                               <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:115px">
                                  <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                  <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                  <button type="submit" style="border:none;background-color: Transparent">
                                     <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                  </button>
                              </form>                
                          @endif
                      @endif
                    @else
                      <!--if user is a student -->
                      @if (count($libraryExist))
                            <button class="btn btn-info btn-raised btn-sm" style="position:absolute;right:92px;top:89px">
                              Added
                            </button>
                        @else
                          <form action=<?php echo url('shoppingcart/addToLibraryOne');?> method="post" style="position:absolute;right:102px;top:89px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                <button  class="btn btn-info btn-raised btn-sm">
                                  Add
                                </button>
                              </form>
                        @endif
                    @endif
                <!--if user is a normal user -->
                 @else
                      <p style="position:absolute;top:90px;left:7px"><font style="font-size:25px;color:#34495E">$<?php echo number_format($price4, 2, '.', '') ;?></font></p>

                  @if (count($libraryExist))
                      <form style="position:absolute;right:85px;top:91px;border:none">
                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                          <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                          <button type="submit" style="border:none;background-color: Transparent">
                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already purchased"></i>
                          </button>
                      </form>
                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:105px"></i>
                  @else
                      <form action=<?php echo URL::route('payment');?> method="post" style="position:absolute;right:85px;top:91px;border:none">
                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                          <input type="hidden" name="fidStr" value=<?php echo $fid4Str;?>>
                          <input type="hidden" name="totalPrice" id="totalPrice" value= <?php echo $price4;?>/>
                          <button type="submit" style="border:none;background-color: Transparent">
                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                          </button>
                      </form>
                  @endif


                  @if (count($shoppingcartExist))
                      <form style="position:absolute;right:86px;top:115px">
                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                          <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                          <button type="submit" style="border:none;background-color: Transparent">
                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already added"></i>
                          </button>
                      </form>
                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:137px"></i>
                  @else
                       <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:115px">
                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                          <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                          <button type="submit" style="border:none;background-color: Transparent">
                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                          </button>
                      </form>                
                  @endif

                 @endif

                    
                    @if($hasSample)
										<div style="position:absolute;right:-3px;top:108px">
                                   <button type="submit" style="border:none;background-color: Transparent">
                        <a href="{{route('getsampleviewer', $ebook->sample_id)}}" class="btn-raised btn-xs btn-info btn">Sample</a> 
                        </button>
                    </div>
                    <p style="position:absolute;right:12px;top:76px">
                        <button class="btn btn-info btn-raised btn-xs slide_open"  onclick="passtoSlide(<?php echo $fid2;?>,'<?php echo $oriFilename2;?>',<?php echo $price2;?>,'<?php echo $description2;?>')">
                        Info
                        </button>
                    </p>
                    @endif
                    
                    @if(!$hasSample)
                        <p style="position:absolute;right:12px;top:89px">
                            <button class="btn btn-info btn-raised btn-sm slide_open"  onclick="passtoSlide(<?php echo $fid2;?>,'<?php echo $oriFilename2;?>',<?php echo $price2;?>,'<?php echo $description2;?>')">
                                Info
                            </button>
                        </p>
                    @endif
                
              </div>
            </div>
          </div>

        @endforeach
      </div>
    </div>


    <!--Best Sellers in Leadership-->
<br/>
         <div class="row">
          <div class="col-sm-2">
        <font style="font-family:Book Antiqua;font-weight:10;position:absolute;left:40px;top:0px" size="6">Leadership</font>
        <hr style="">
          </div>
        </div><br/>
        
        
        <?php

         $leadershipEbooks = \DB::table('libraries')
          ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
          ->where('category', 'Leadership')
          ->select(DB::raw('count(*) as fileentry_count'),'fileentries.*', 'libraries.fileentry_id')
          ->groupBy('libraries.fileentry_id')
          ->orderBy('fileentry_count', 'desc')
          ->take(4)
          ->get();



          // $financialEbooks = \DB::table('fileentries')
          // ->take(8)
          // ->get();

          $countLeadershipEbooks = count($leadershipEbooks); 
          if($countLeadershipEbooks<2) {
              $leadershipEbooks = \DB::table('fileentries')
              ->where('category', 'Leadership')
              ->take(4)
              ->get();
          }

        ?>

      <div class="row">
        <div class="col-sm-10">
        <?php
            $countNum6 = 0;
            $filenameArr6 = array();
        ?>
        @foreach($leadershipEbooks as $ebook)
         <?php
          $countNum6++;

          $filename6 = $ebook->filename;
          array_push($filenameArr6,$filename6);
          $container6 = "6container".$countNum6;

          $price4 = $ebook->price; 
          $fid4 = $ebook->id;
          $fid4Str = (string) $fid4; //convert to string so that i can pass to payment side when user purchase book
          $fid4Str = ",".$fid4Str;
          
          $oriFilename4 = $ebook->original_filename;
          $description4= $ebook ->description;

          //whether the book is in shoppingcart or not / library
          $shoppingcartExist = \DB::table('shoppingcarts')
          ->where('user_id', Auth::user()->id)
          ->where('fileentry_id', $fid4)
          ->get();

          $libraryExist = \DB::table('libraries')
          ->where('user_id', Auth::user()->id)
          ->where('fileentry_id', $fid4)
          ->get();

          // check if this user is a student in a course that requires this book
          $coursesOfThisBook = \DB::table('courseMaterial')
          ->where('fileEntriesID', $ebook->id)
          ->pluck('courseID');

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
          ?>                      

           <div class="col-sm-6 col-md-3">
            <div class="thumbnail" style="height:365px;width:210px">
              <div id=<?php echo $container6;?> style="position:relative;height:200px;width:135px;margin: 0 auto;"></div>
                               
              <div class="caption" style="position:relative">
                  <div style="position:relative;margin: 0 auto;">
                      @if(strlen($oriFilename4) > 25)
                      <p>{{substr($oriFilename4,0,25)."..." }}</p>
                      @else
                      <p>{{$oriFilename4}}</p>
                      @endif
                  <font size="1">Category: <?php echo $ebook->category;?></font>
                 </div>

                  @if($isSubscribe || $isStudent)
                    <!-- if user is subscriber -->
                    @if($isSubscribe)
                      @if($notYetExpired)
                        @if (count($libraryExist))
                            <button  class="btn btn-info btn-raised btn-sm" style="position:absolute;right:92px;top:89px">
                              Added 
                            </button>
                        @else
                          <form action=<?php echo url('shoppingcart/addToLibraryOne');?> method="post" style="position:absolute;right:102px;top:89px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                <button  class="btn btn-info btn-raised btn-sm">
                                  Add
                                </button>
                              </form>
                        @endif
                      @else
                        <p style="position:absolute;top:90px;left:7px"><font style="font-size:25px;color:#34495E">$<?php echo number_format($price4, 2, '.', '') ;?></font></p>

                        @if (count($libraryExist))
                            <form style="position:absolute;right:85px;top:91px;border:none">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                <button type="submit" style="border:none;background-color: Transparent">
                                   <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already purchased"></i>
                                </button>
                            </form>
                            <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:105px"></i>
                        @else
                            <form action=<?php echo URL::route('payment');?> method="post" style="position:absolute;right:85px;top:91px;border:none">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fidStr" value=<?php echo $fid4Str;?>>
                                <input type="hidden" name="totalPrice" id="totalPrice" value=<?php echo $price4;?>/>
                                <button type="submit" style="border:none;background-color: Transparent">
                                   <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                </button>
                            </form>
                        @endif


                        @if (count($shoppingcartExist))
                            <form style="position:absolute;right:86px;top:105px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                <button type="submit" style="border:none;background-color: Transparent">
                                   <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already added"></i>
                                </button>
                            </form>
                            <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:127px"></i>
                        @else
                             <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:115px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                <button type="submit" style="border:none;background-color: Transparent">
                                   <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                </button>
                            </form>                
                        @endif
                      @endif
                    @else
                      <!-- if user is a student -->
                      @if (count($libraryExist))
                            <button  class="btn btn-info btn-raised btn-sm" style="position:absolute;right:92px;top:89px">
                              Added 
                            </button>
                        @else
                          <form action=<?php echo url('shoppingcart/addToLibraryOne');?> method="post" style="position:absolute;right:102px;top:89px">
                                <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                                <button  class="btn btn-info btn-raised btn-sm">
                                  Add
                                </button>
                              </form>
                        @endif
                    @endif
                  @else
                  <!-- if user is a normal user -->
                  <p style="position:absolute;top:90px;left:7px"><font style="font-size:25px;color:#34495E">$<?php echo number_format($price4, 2, '.', '');?></font></p>

                    @if (count($libraryExist))
                        <form style="position:absolute;right:85px;top:91px;border:none">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already purchased"></i>
                            </button>
                        </form>
                        <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:105px"></i>
                    @else
                        <form action=<?php echo URL::route('payment');?> method="post" style="position:absolute;right:85px;top:91px;border:none">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fidStr" value=<?php echo $fid4Str;?>>
                            <input type="hidden" name="totalPrice" id="totalPrice" value=<?php echo $price4;?>/>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                            </button>
                        </form>
                    @endif


                    @if (count($shoppingcartExist))
                        <form style="position:absolute;right:86px;top:105px">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Already added"></i>
                            </button>
                        </form>
                        <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:127px"></i>
                    @else
                         <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:115px">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fid" value=<?php echo $fid4;?>>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                            </button>
                        </form>                
                    @endif
                  @endif          
                   @if($hasSample)
										<div style="position:absolute;right:-3px;top:108px">
                                   <button type="submit" style="border:none;background-color: Transparent">
                        <a href="{{route('getsampleviewer', $ebook->sample_id)}}" class="btn-raised btn-xs btn-info btn">Sample</a> 
                        </button>
                    </div>
                    <p style="position:absolute;right:12px;top:76px">
                        <button class="btn btn-info btn-raised btn-xs slide_open"  onclick="passtoSlide(<?php echo $fid2;?>,'<?php echo $oriFilename2;?>',<?php echo $price2;?>,'<?php echo $description2;?>')">
                        Info
                        </button>
                    </p>
                    @endif
                    
                    @if(!$hasSample)
                        <p style="position:absolute;right:12px;top:89px">
                            <button class="btn btn-info btn-raised btn-sm slide_open"  onclick="passtoSlide(<?php echo $fid2;?>,'<?php echo $oriFilename2;?>',<?php echo $price2;?>,'<?php echo $description2;?>')">
                                Info
                            </button>
                        </p>
                    @endif
                
              </div>
            </div>
          </div>

        @endforeach
      </div>
    </div>


        
   </div>      

    
    <!-- /.container -->
    

    <!-- Slide in popup window-->

          <div id="slide" class="well" style="position:relative;top:30px;width:600px;height:400px">
              <button class="slide_close btn btn-default" style="position:absolute;right:20px"><i class="fa fa-times" aria-hidden="true"></i></button>
              <br/>
              <span id="fid" hidden></span><br/>
              <font size="5" color="black"><span id="title"></span></font><br/><br/>
              <font size="5" color="black" style="position:absolute;right:230px;">$<span id="price"></span></font><br/><br/>
              <p><font size="3" color="black">&nbsp&nbsp<span id="description"></span></font></p><br/>
             
          </div>
    <!-- -->


    <!-- Basic Slider-->

    <?php
      $subscriptionPlans = \DB::table('subscriptionplan')
              ->get();

    ?>

     <div id="basic" class="well" style="max-width:74em;">
        <h4><font color='black'>Choose a subscribtion plan:</font></h4>
      <form action=<?php echo url('/subscribe');?>  method="post">
        @foreach($subscriptionPlans as $plan)
        <p><input type="radio" onclick="pay()" name="period" id=<?php echo $plan->monthperiod?> value=<?php echo $plan->monthperiod?> checked><font color='black'> <?php echo $plan->monthperiod?> days at $<?php echo $plan->price?></font></p>
        <!-- <p><input type="radio" name="period" value="1" checked><font color='black'> 1 month</font></p>
        <p><input type="radio" name="period" value="6"><font color='black'> 6 months</font></p>
        <p><input type="radio" name="period" value="12"><font color='black'> 1 year</font></p> -->
         @endforeach
        <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
        <input type="hidden" id="amount" name="amount" value="" />
        <input type="hidden" id="period" name="period" value="" />
        <button type="submit" class="btn-default btn">Subscribe</button>
        <button class="basic_close btn btn-default">Cancel</button>
      </form>
    </div>
    <!--end of basic slider-->




   

<script>
    function getPreview(url, divId) {
        PDFJS.getDocument(url)
        .then(function(pdf) {
                    // Get div#container and cache it for later use
                    //var container = document.getElementById(divId);
                    var container = document.getElementById(divId);

                    // Loop from 1 to total_number_of_pages in PDF document
                    var i = 1;

                        // Get desired page
                        pdf.getPage(i).then(function(page) {

                          // Create a new Canvas element
                          var canvas = document.createElement("canvas");

                          var scale = 1.5;
                          var viewport = page.getViewport(canvas.width / page.getViewport(2.2).width);
                          var div = document.createElement("div");

                          // Set id attribute with page-#{pdf_page_number} format
                          div.setAttribute("id", "page-" + (page.pageIndex + 1));

                          // This will keep positions of child elements as per our needs
                          div.setAttribute("style", "position: relative");

                          // Append div within div#container
                          container.appendChild(div);

                          

                          // Append Canvas within div#page-#{pdf_page_number}
                          div.appendChild(canvas);

                          var context = canvas.getContext('2d');
                          canvas.height = viewport.height;  //1188
                          canvas.width = viewport.width;   //918
                          var renderContext = {
                            canvasContext: context,
                            viewport: viewport
                          };

                          // Render PDF page
                          page.render(renderContext)
                          .then(function() {
                            // Get text-fragments
                            return page.getTextContent();
                          })
                        });
                    
                      });



    }

    function passtoSlide(fid, title, price, description) {
      var fid =fid;
      var title= title;
      var price = price;
      var description = description;

      document.getElementById("fid").innerHTML = fid;
      document.getElementById("title").innerHTML = title;
      document.getElementById("price").innerHTML = price;
      document.getElementById("description").innerHTML = description;


    }

</script>
   
 <script>
    
               
</script> 



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


<!--For displaying pdfs-->
  <script>
    <?php
        $js_array = json_encode($filenameArr2);
        echo "var filename_array2 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($trendingEbooks)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;
              
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array2[y - 1];
        var divId = "2container" + y;
        getPreview(url, divId);
    }             
               
</script> 

<script>
    <?php
        $js_array = json_encode($filenameArr4);
        echo "var filename_array4 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($financialEbooks)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;         
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array4[y - 1];
        var divId = "4container" + y;
        getPreview(url, divId);
    }             
               
</script> 

<script>
    <?php
        $js_array = json_encode($filenameArr6);
        echo "var filename_array6 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($leadershipEbooks)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;       
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array6[y - 1];
        var divId = "6container" + y;
        getPreview(url, divId);
    } 


//to get the price of the selected subscription plan upon clicking
     function pay() {
     		var subscriptionPlans = <?php echo json_encode($subscriptionPlans)?>;
      		var price = "";
      		var period = "";
     		var mainUrl = window.location.hostname;
     		var countFinal = 0;
              console.log("h");
            var count = <?php echo count($subscriptionPlans)?>;
     		for(i = 0; i < count; i++) {
     			if(document.getElementById(subscriptionPlans[i].monthperiod).checked) {
     			    price= subscriptionPlans[i].price;
     			    period = subscriptionPlans[i].monthperiod;
     console.log(price);

     			}
     		}

            // countTotalprice();
           //  document.querySelector('#totalPrice').value = totalprice;

            document.querySelector('#period').value = period;
            document.querySelector('#amount').value = price;


     	}	
               
</script>