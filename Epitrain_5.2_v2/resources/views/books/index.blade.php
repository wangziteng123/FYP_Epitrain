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
                    <h1><font color="black">All books in shop</font></h1>
                  <?php } else { ?>
                    <h1><font color="black">Books under <?php echo $chosenCategory['cat'];?> category</font></h1>
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

                    ?>
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
                            <div class="col-sm-3 col-xs-3 col-xs-offset-2 hidden-xs">
                                @if (count($libraryExist))
                                  <button class="btn btn-warning" style="background-color: darkblue; color:yellow">
                                      <font style="">Bought Already</font>
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
                    </div>
                @endforeach

            </div>
        </div>
        {{ $bookList->links() }}
    </div>
</div>
<link rel="stylesheet" href="{{ URL::asset('css/style.css') }}" />
@endsection



