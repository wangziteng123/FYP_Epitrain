@extends('layouts.app')

@section('content')


<script>

function categoryButton(category) {
  var mainUrl = window.location.hostname;
  var url = "http://" + mainUrl + "/category?category=" + category;

  window.location = url;
}

</script>

    <!-- Split button -->

   <!-- Page Content -->
    <div class="container" style="positon:relative">

        @if (Auth::user()->isAdmin)
        @else
        
          <div class="fixed-action-btn toolbar" >
						<a class="btn-floating btn-large" style = "background-color: #062C94">
							<i class="large material-icons">view_array</i>
						</a>
            <!--<div style="position:relative;top:15px">-->
            <!--<font color="#1034A6">-->
						<ul>
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">Trading</span></a></li>
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">Risk Management</span></a></li>
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">Fintech</span></a></li>
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">Project Management</span></a></li>
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">Finance</span></a></li>
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">Business Management</span></a></li>
							
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">Leadership</span></a></li>
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">Financial Market</span></a></li>
							
							<li class="waves-effect waves-light"><a href='/home'> <span class="white-text text-darken-2">View All</span></a></li>
            <!--</font>-->
						</ul>
          </div>
    </div>
        

        @endif

        <div style="">
          <br/>
          <br/>
        </div>
        <div style="color:blue">
          <h2>Epitrain Elearning Platform</h2>
        </div>

        <?php
         use Carbon\Carbon;

         $isSubscribe = Auth::user()->subscribe;
       ?>

      @if (!Auth::user()->isAdmin)
      

        @if($isSubscribe)
          <?php
            $currentTime = Carbon::now();
            $userSubscribe = \DB::table('subscription')
              ->where('user_id', Auth::user()->id)
              ->first();

            $end_Date = Carbon::createFromFormat('Y-m-d H:i:s', $userSubscribe->end_date);
            $expireOrnot = $currentTime->lt($end_Date);
          ?>
          @if($expireOrnot)
            <div style = "color:black">
            Your subscription plan will end at <?php echo $end_Date->toDateTimeString();?>.
            </div>
          @else
          <div>
           Want to start a subscription? &nbsp&nbsp
          <button  class="btn btn-four initialism basic_open" style="width:150px;">
            SUBSCRIBE
          </button>
         </div>

          @endif
        @else
          <div>
           Want to start a subscription? &nbsp&nbsp
          <button  class="btn btn-four initialism basic_open" style="width:150px;">
            SUBSCRIBE
          </button>
         </div>

        @endif
      @endif

        

      <div class="container">
        <!--Trending-->
        <div style="position:relative" class="col s12">
        <font style="color:darkblue;position:absolute;left:40px;top:0px" size="6">Trending </font>
        </div><br/>
        <hr>

        <!--Ninja Slider1-->
        <?php

          $trendingEbooks = \DB::table('libraries')
          ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
          ->select(DB::raw('count(*) as fileentry_count'),'fileentries.*', 'libraries.fileentry_id')
          ->groupBy('libraries.fileentry_id')
          ->orderBy('fileentry_count', 'desc')
          ->take(8)
          ->get();


          // $trendingEbooks = \DB::table('fileentries')
          // ->take(8)
          // ->get();


          $countTrendingEbooks = count($trendingEbooks); 
          $trendingEbooks1 = [];
          $trendingEbooks2 = [];

          $countFor = 0;
          foreach($trendingEbooks as $ebook) {
            $countFor ++;
            if($countFor > 4) {
              array_push($trendingEbooks2, $ebook);
            } else {
              array_push($trendingEbooks1, $ebook);
            }

          }

        ?>


          <div class="row text-center col s12" style="border-style: solid;border-width: 4px;">
            <div id="ninja-slider">
                <div class="slider-inner">
                    <ul>
										<!--first page of trending ebook-->
                       <li>
                         <div class="content">
                              <?php
                                $countNum2 = 0;
                                $filenameArr2 = array();
                              ?>
                              @foreach($trendingEbooks1 as $ebook)
                              <?php
                                $countNum2++;

                                $filename2 = $ebook->filename;
                                array_push($filenameArr2,$filename2);
                                $container2 = "2container".$countNum2;

                                $price2 = $ebook->price; 
                                $fid2 = $ebook->id;
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
                             
                              ?>

                          <div class="col-md-3 col-sm-10 hero-feature" style="">

                          <div class="thumbnail" style="position:relative;height:365px;width:200px">
                              <div id=<?php echo $container2?> style="position:relative;left:22px;height:200px;width:135px"></div>
                               
                              <div class="caption" style="">
                                  <div style="position:absolute;top:195px">
                                      @if(strlen($oriFilename2) > 30)
                                      <p>{{substr($oriFilename2,0,30)."..." }}</p>
                                      @else
                                      <p>{{$oriFilename2}}</p>
                                      @endif
                                  <font size="1">Category: <?php echo $ebook->category?></font>
                                 </div>

                                
                                  <p style="position:absolute;top:274px;left:15px"><font style="font-size:25px;color:#34495E">S$<?php echo $price2?></font></p>

                                  @if (count($libraryExist))
                                      <form style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid2?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Bought already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:289px"></i>
                                  @else
                                      <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post" style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid2?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                          </button>
                                      </form>
                                  @endif


                                  @if (count($shoppingcartExist))
                                      <form style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid2?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Added already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:311px"></i>
                                  @else
                                       <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid2?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                          </button>
                                      </form>                
                                  @endif
                                  
                                    
                                  <p style="position:absolute;right:12px;top:283px;">
                                      <button class="btn waves-effect waves-light btn blue slide_open" style="height:38px;width:65px" onclick="passtoSlide(<?php echo $fid2;?>,'<?php echo $oriFilename2;?>',<?php echo $price2;?>,'<?php echo $description2;?>')">
                                         Info
                                      </button>
                                  </p>
                                
                              </div>
                            </div>
                          </div>
                      @endforeach
                  </div>
              </li>
										<!--second page of trending ebook-->
                       <li>
                            <div class="content">
                               <?php
                                $countNum3 = 0;
                                $filenameArr3 = array();
                              ?>
                              @foreach($trendingEbooks2 as $ebook3)
                              <?php
                                $countNum3++;

                                $filename3 = $ebook3->filename;
                                array_push($filenameArr3,$filename3);
                                $container3 = "3container".$countNum3;

                                $price3 = $ebook3->price; 
                                $fid3 = $ebook3->id;
                                $oriFilename3 = $ebook3->original_filename;
                                $description3= $ebook3 ->description;

                                 //whether the book is in shoppingcart or not / library
                                $shoppingcartExist = \DB::table('shoppingcarts')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid3)
                                ->get();

                                $libraryExist = \DB::table('libraries')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid3)
                                ->get();

                                $container3 = "3container".$countNum3;
                             
                              ?>

                          <div class="col-md-3 col-sm-10 hero-feature" style="">

                          <div class="thumbnail" style="position:relative;height:365px;width:200px">
                              <div id=<?php echo $container3?> style="position:relative;left:22px;height:200px;width:135px"></div>
                               
                              <div class="caption" style="">
                                  <div style="position:absolute;top:195px">
                                      @if(strlen($oriFilename3) > 30)
                                      <p>{{substr($oriFilename3,0,30)."..." }}</p>
                                      @else
                                      <p>{{$oriFilename3}}</p>
                                      @endif
                                  <font size="1">Category: <?php echo $ebook3->category?></font>
                                 </div>

                                
                                  <p style="position:absolute;top:274px;left:15px"><font style="font-size:25px;color:#34495E">S$<?php echo $price3?></font></p>

                                  @if (count($libraryExist))
                                      <form style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid3?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Bought already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:289px"></i>
                                  @else
                                      <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post" style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid3?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                          </button>
                                      </form>
                                  @endif


                                  @if (count($shoppingcartExist))
                                      <form style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid3?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Added already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:311px"></i>
                                  @else
                                       <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid3?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                          </button>
                                      </form>                
                                  @endif
                                  
                                    
                                  <p style="position:absolute;right:12px;top:283px">
                                      <button  class="btn waves-effect waves-light btn blue slide_open" style="height:38px;width:65px" onclick="passtoSlide(<?php echo $fid3;?>,'<?php echo $oriFilename3;?>',<?php echo $price3;?>,'<?php echo $description3;?>')">
                                         Info
                                      </button>
                                  </p>
                                </div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    </li>
									</ul>
                <div class="fs-icon" title="Expand/Close"></div>
							<div id="current-slide-num"></div>
            </div>
          </div
      </div>
    </div>
    <!--end of slider-->
  <br/>


        <!--Best Sellers in Financial Market-->
        <div style="position:relative">
        <font style="color:darkblue;position:absolute;left:40px;top:0px" size="6">Best Sellers in Financial Market </font>
        </div><br/>
        <hr>

        <!--Ninja Slider1-->
        <?php

          $financialEbooks = \DB::table('libraries')
          ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
          ->where('category', 'FinancialMarket')
          ->select(DB::raw('count(*) as fileentry_count'),'fileentries.*', 'libraries.fileentry_id')
          ->groupBy('libraries.fileentry_id')
          ->orderBy('fileentry_count', 'desc')
          ->take(8)
          ->get();


          // $financialEbooks = \DB::table('fileentries')
          // ->take(8)
          // ->get();


          $countTrendingEbooks = count($financialEbooks); 
          $financialEbooks1 = [];
          $financialEbooks2 = [];

          $countFor2 = 0;
          foreach($financialEbooks as $fEbook) {
            $countFor2 ++;
            if($countFor2 > 4) {
              array_push($financialEbooks2, $fEbook);
            } else {
              array_push($financialEbooks1, $fEbook);
            }

          }

        ?>


          <div class="row text-center" style="border-style:solid;border-width: 4px;">

          
            <div id="ninja-slider2">
                <div class="slider-inner" style="width:1160px">
                    <ul>

                        <li>
                            <div class="content" style="width:1170px">
                              <?php
                                $countNum4 = 0;
                                $filenameArr4 = array();
                              ?>
                              @foreach($financialEbooks1 as $fEbook)
                              <?php
                                $countNum4++;

                                $filename4 = $fEbook->filename;
                                array_push($filenameArr4,$filename4);
                                $container4 = "4container".$countNum4;

                                $price4 = $fEbook->price; 
                                $fid4 = $fEbook->id;
                                $oriFilename4 = $fEbook->original_filename;
                                $description4= $fEbook ->description;

                                 //whether the book is in shoppingcart or not / library
                                $shoppingcartExist4 = \DB::table('shoppingcarts')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid4)
                                ->get();

                                $libraryExist4 = \DB::table('libraries')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid4)
                                ->get();

                                //$container4 = "4container".$countNum4;
                             
                              ?>

                          <div class="col-md-3 col-sm-10 hero-feature" style="">

                          <div class="thumbnail" style="position:relative;height:365px;width:200px">
                              <div id=<?php echo $container4?> style="position:relative;left:22px;height:200px;width:135px"></div>
                               
                              <div class="caption" style="">
                                  <div style="position:absolute;top:195px">
                                      @if(strlen($oriFilename4) > 30)
                                      <p>{{substr($oriFilename4,0,30)."..." }}</p>
                                      @else
                                      <p>{{$oriFilename4}}</p>
                                      @endif
                                  <font size="1">Category: <?php echo $fEbook->category?></font>
                                 </div>

                                
                                  <p style="position:absolute;top:274px;left:15px"><font style="font-size:25px;color:#34495E">S$<?php echo $price4?></font></p>

                                  @if (count($libraryExist4))
                                      <form style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid4?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Bought already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:289px"></i>
                                  @else
                                      <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post" style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid4?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                          </button>
                                      </form>
                                  @endif


                                  @if (count($shoppingcartExist4))
                                      <form style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid4?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Added already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:311px"></i>
                                  @else
                                       <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid4?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                          </button>
                                      </form>                
                                  @endif
                                  
                                    
                                  <p style="position:absolute;right:12px;top:283px">
                                      <button  class="btn waves-effect waves-light btn blue slide_open" style="height:38px;width:65px" onclick="passtoSlide(<?php echo $fid4;?>,'<?php echo $oriFilename4;?>',<?php echo $price4;?>,'<?php echo $description4;?>')">
                                         Info
                                      </button>
                                  </p>
                                
                              </div>
                          </div>

                          </div>


                              @endforeach
                            </div>
                        </li>

                        <li>
                            <div class="content">
                               <?php
                                $countNum5 = 0;
                                $filenameArr5 = array();
                              ?>
                              @foreach($financialEbooks2 as $fEbook)
                              <?php
                                $countNum5++;

                                $filename5 = $fEbook->filename;
                                array_push($filenameArr5,$filename5);
                                $container5 = "5container".$countNum5;

                                $price5 = $fEbook->price; 
                                $fid5 = $fEbook->id;
                                $oriFilename5 = $fEbook->original_filename;
                                $description5 = $fEbook ->description;

                                 //whether the book is in shoppingcart or not / library
                                $shoppingcartExist5 = \DB::table('shoppingcarts')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid5)
                                ->get();

                                $libraryExist5 = \DB::table('libraries')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid5)
                                ->get();

                                //$container5 = "5container".$countNum5;
                             
                              ?>

                          <div class="col-md-3 col-sm-10 hero-feature" style="">

                          <div class="thumbnail" style="position:relative;height:365px;width:200px">
                              <div id=<?php echo $container5?> style="position:relative;left:22px;height:200px;width:135px"></div>
                               
                              <div class="caption" style="">
                                  <div style="position:absolute;top:195px">
                                      @if(strlen($oriFilename5) > 30)
                                      <p>{{substr($oriFilename5,0,30)."..." }}</p>
                                      @else
                                      <p>{{$oriFilename5}}</p>
                                      @endif
                                  <font size="1">Category: <?php echo $fEbook->category?></font>
                                 </div>

                                
                                  <p style="position:absolute;top:274px;left:15px"><font style="font-size:25px;color:#34495E">S$<?php echo $price5?></font></p>

                                  @if (count($libraryExist5))
                                      <form style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid5?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Bought already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:289px"></i>
                                  @else
                                      <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post" style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid5?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                          </button>
                                      </form>
                                  @endif


                                  @if (count($shoppingcartExist5))
                                      <form style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid5?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Added already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:311px"></i>
                                  @else
                                       <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid5?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                          </button>
                                      </form>                
                                  @endif
                                  
                                    
                                  <p style="position:absolute;right:12px;top:283px">
                                      <button  class="btn waves-effect waves-light btn blue slide_open" style="height:38px;width:65px" onclick="passtoSlide(<?php echo $fid5;?>,'<?php echo $oriFilename5;?>',<?php echo $price5;?>,'<?php echo $description5;?>')">
                                         Info
                                      </button>
                                  </p>
                                
                              </div>
                          </div>

                          </div>


                              @endforeach



                            </div>
                        </li>

                    </ul>
                    <div class="fs-icon" title="Expand/Close"></div>
                </div>
            </div>

        </div>
        <!--end of slider-->
        <!--End of Best Sellers in Financial Market-->


        <br/>
        <!--Best Sellers in Leadership-->
        <div style="position:relative">
        <font style="color:darkblue;position:absolute;left:40px;top:0px" size="6">Best Sellers in Leadership </font>
        </div><br/>
        <hr>

        <!--Ninja Slider1-->
        <?php

          $leadershipEbooks = \DB::table('libraries')
          ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
          ->where('category', 'Leadership')
          ->select(DB::raw('count(*) as fileentry_count'),'fileentries.*', 'libraries.fileentry_id')
          ->groupBy('libraries.fileentry_id')
          ->orderBy('fileentry_count', 'desc')
          ->take(8)
          ->get();


          // $trendingEbooks = \DB::table('fileentries')
          // ->take(8)
          // ->get();


          $countLeadershipEbooks = count($leadershipEbooks); 
          $leadershipEbooks1 = [];
          $leadershipEbooks2 = [];

          $countFor3 = 0;
          foreach($leadershipEbooks as $Lbook) {
            $countFor3 ++;
            if($countFor3 > 4) {
              array_push($leadershipEbooks2, $Lbook);
            } else {
              array_push($leadershipEbooks1, $Lbook);
            }

          }

        ?>


          <div class="row text-center" style="border-style: solid;border-width: 4px;">

          
            <div id="ninja-slider3">
                <div class="slider-inner" style="width:1170px">
                    <ul>

                        <li>
                            <div class="content" style="width:1170px">
                              <?php
                                $countNum6 = 0;
                                $filenameArr6 = array();
                              ?>
                              @foreach($leadershipEbooks1 as $lEbook)
                              <?php
                                $countNum6++;

                                $filename6 = $lEbook->filename;
                                array_push($filenameArr6,$filename6);
                                $container6 = "6container".$countNum6;

                                $price6 = $lEbook->price; 
                                $fid6 = $lEbook->id;
                                $oriFilename6 = $lEbook->original_filename;
                                $description6 = $lEbook ->description;

                                 //whether the book is in shoppingcart or not / library
                                $shoppingcartExist6 = \DB::table('shoppingcarts')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid6)
                                ->get();

                                $libraryExist6 = \DB::table('libraries')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid6)
                                ->get();

                                //$container4 = "4container".$countNum4;
                             
                              ?>

                          <div class="col-md-3 col-sm-10 hero-feature" style="">

                          <div class="thumbnail" style="position:relative;height:365px;width:200px">
                              <div id=<?php echo $container6?> style="position:relative;left:22px;height:200px;width:135px"></div>
                               
                              <div class="caption" style="">
                                  <div style="position:absolute;top:195px">
                                      @if(strlen($oriFilename6) > 30)
                                      <p>{{substr($oriFilename4,0,30)."..." }}</p>
                                      @else
                                      <p>{{$oriFilename6}}</p>
                                      @endif
                                  <font size="1">Category: <?php echo $lEbook->category?></font>
                                 </div>

                                
                                  <p style="position:absolute;top:274px;left:15px"><font style="font-size:25px;color:#34495E">S$<?php echo $price6?></font></p>

                                  @if (count($libraryExist6))
                                      <form style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid6?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Bought already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:289px"></i>
                                  @else
                                      <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post" style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid6?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                          </button>
                                      </form>
                                  @endif


                                  @if (count($shoppingcartExist6))
                                      <form style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid6?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Added already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:311px"></i>
                                  @else
                                       <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid6?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                          </button>
                                      </form>                
                                  @endif
                                  
                                    
                                  <p style="position:absolute;right:12px;top:283px">
                                      <button  class="btn waves-effect waves-light btn blue slide_open" style="height:38px;width:65px" onclick="passtoSlide(<?php echo $fid6;?>,'<?php echo $oriFilename6;?>',<?php echo $price6;?>,'<?php echo $description6;?>')">
                                         Info
                                      </button>
                                  </p>
                                
                              </div>
                          </div>

                          </div>


                              @endforeach
                            </div>
                        </li>

                        <li>
                            <div class="content">
                               <?php
                                $countNum7 = 0;
                                $filenameArr7 = array();
                              ?>
                              @foreach($leadershipEbooks2 as $lEbook)
                              <?php
                                $countNum7++;

                                $filename7 = $lEbook->filename;
                                array_push($filenameArr7,$filename7);
                                $container7 = "7container".$countNum7;

                                $price7 = $lEbook->price; 
                                $fid7 = $lEbook->id;
                                $oriFilename7 = $lEbook->original_filename;
                                $description7 = $lEbook ->description;

                                 //whether the book is in shoppingcart or not / library
                                $shoppingcartExist7 = \DB::table('shoppingcarts')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid5)
                                ->get();

                                $libraryExist7 = \DB::table('libraries')
                                ->where('user_id', Auth::user()->id)
                                ->where('fileentry_id', $fid7)
                                ->get();

                                //$container5 = "5container".$countNum5;
                             
                              ?>

                          <div class="col-md-3 col-sm-10 hero-feature" style="">

                          <div class="thumbnail" style="position:relative;height:365px;width:200px">
                              <div id=<?php echo $container7?> style="position:relative;left:22px;height:200px;width:135px"></div>
                               
                              <div class="caption" style="">
                                  <div style="position:absolute;top:195px">
                                      @if(strlen($oriFilename7) > 30)
                                      <p>{{substr($oriFilename7,0,30)."..." }}</p>
                                      @else
                                      <p>{{$oriFilename7}}</p>
                                      @endif
                                  <font size="1">Category: <?php echo $lEbook->category?></font>
                                 </div>

                                
                                  <p style="position:absolute;top:274px;left:15px"><font style="font-size:25px;color:#34495E">S$<?php echo $price7?></font></p>

                                  @if (count($libraryExis7))
                                      <form style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid7?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Bought already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:83px;top:289px"></i>
                                  @else
                                      <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post" style="position:absolute;right:85px;top:275px;border:none">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid7?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-bag fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Purchase this book"></i>
                                          </button>
                                      </form>
                                  @endif


                                  @if (count($shoppingcartExist7))
                                      <form style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid7?>>
                                          <button type="button" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Added already."></i>
                                          </button>
                                      </form>
                                      <i class="fa fa-check-circle" aria-hidden="true" style="color:#82E0AA;position:absolute;right:85px;top:311px"></i>
                                  @else
                                       <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:86px;top:299px">
                                          <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                                          <input type="hidden" name="fid" value=<?php echo $fid7?>>
                                          <button type="submit" style="border:none;background-color: Transparent">
                                             <i class="fa fa-shopping-cart fa-lg tooltipTipsy" aria-hidden="true" style="color:#808B96" title="Add to shoppingcart."></i>
                                          </button>
                                      </form>                
                                  @endif
                                  
                                    
                                  <p style="position:absolute;right:12px;top:283px">
                                      <button  class="btn waves-effect waves-light btn blue slide_open" style="height:38px;width:65px" onclick="passtoSlide(<?php echo $fid7;?>,'<?php echo $oriFilename7;?>',<?php echo $price7;?>,'<?php echo $description7;?>')">
                                         Info
                                      </button>
                                  </p>
                                
                              </div>
                          </div>

                          </div>


                              @endforeach



                            </div>
                        </li>

                    </ul>
                    <div class="fs-icon" title="Expand/Close"></div>
                </div>
            </div>

          </div>
        <!--end of slider-->
        <!--End of Best Sellers in Leadership-->
          <br/><br/>
          <div style="position:absoute;bottom:30px">
            <footer>
                <div class="row">
                    <div class="col-lg-12">
                        <p>Copyright &copy; Your Website 2014</p>
                    </div>
                </div>
            </footer>
          </div>
      </div>

        
   </div>      

    
    <!-- /.container -->
    

    <!-- Slide in popup window-->

          <div id="slide" class="well" style="position:relative;top:30px;width:600px;height:400px">
              <button class="slide_close btn btn-default" style="position:absolute;right:20px"><i class="fa fa-times" aria-hidden="true"></i></button>
              <br/>
              <span id="fid" hidden></span><br/>
              <font size="5"><span id="title"></span></font><br/><br/>
              <font size="5" color="#34495E" style="position:absolute;right:230px;">$<span id="price"></span></font><br/><br/>
              <p><font size="3">&nbsp&nbsp<span id="description"></span></font></p><br/>
             
          </div>

      <!-- Basic Slider-->

    <div id="basic" class="well" style="max-width:74em;">
        <h4>Choose a subscribtion plan:</h4>
      <form action=<?php echo url('/subscribe');?>  method="post">
        <p><input type="radio" name="period" value="1" checked> 1 month</p>
        <p><input type="radio" name="period" value="6"> 6 months</p>
        <p><input type="radio" name="period" value="12"> 1 year</p>
        <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
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
    <?php
        $js_array = json_encode($filenameArr2);
        echo "var filename_array2 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($trendingEbooks1)?>;        
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
        $js_array = json_encode($filenameArr3);
        echo "var filename_array3 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($trendingEbooks2)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;          
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array3[y - 1];
        var divId = "3container" + y;
        getPreview(url, divId);
    }             
               
</script> 

<script>
    <?php
        $js_array = json_encode($filenameArr4);
        echo "var filename_array4 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($financialEbooks1)?>;        
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
        $js_array = json_encode($filenameArr5);
        echo "var filename_array5 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($financialEbooks2)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;       
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array5[y - 1];
        var divId = "5container" + y;
        getPreview(url, divId);
    }             
               
</script> 

<script>
    <?php
        $js_array = json_encode($filenameArr6);
        echo "var filename_array6 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($leadershipEbooks1)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;       
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array6[y - 1];
        var divId = "6container" + y;
        getPreview(url, divId);
    }             
               
</script> 

<script>
    <?php
        $js_array = json_encode($filenameArr7);
        echo "var filename_array7 = ". $js_array . ";\n";
    ?>
    var countEntries = <?php echo count($leadershipEbooks2)?>;        
    // URL of PDF document
    var mainUrl = window.location.hostname;       
           
    for(y = 1; y <= countEntries; y++) {
        var url = "http://" + mainUrl + "/fileentry/get/" + filename_array7[y - 1];
        var divId = "7container" + y;
        getPreview(url, divId);
    }             
               
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

@endsection


