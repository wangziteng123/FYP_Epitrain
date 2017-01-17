@extends('layouts.app')

@section('content')


    <!-- Split button -->

   <!-- Page Content -->
    <div class="container">

        <!-- Jumbotron Header -->
        <!-- <header class="jumbotron hero-spacer" style="min-height: 300px;">
            <h1><font color="black">A Warm Welcome!</font></h1>
            <p><font style="color:black;font-weight:600" >Epitrain® is a provider of training, resource development and consultancy services. </font></p>
            <p>

                <div class="well" style="width:500px; margin:0 auto;">
                    <h4 style="color:black">Find the book you want</h4>
                    <div class="input-group">
                        <input type="text" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <span class="glyphicon glyphicon-search"></span>
                        </button>
                        </span>
                    </div>
                </div>
            </p>
        </header> -->

        <hr>

        <!-- Title -->
        <div class="row">
            <div class="col-lg-12">
                <h3>Suggestions</h3>
            </div>
        </div>
        <!-- /.row -->

        <!-- Page Features -->
        <div class="row text-center">


            @foreach($entries as $entry)
                <?php
                     $price = $entry->price; 
                     $fid = $entry->id;
                     $imgSrc = $entry->filename;
                     $pos = strpos($imgSrc, "pdf");
                     $imgSrc = substr($imgSrc, 0, $pos);
                     $imgSrc = "img/".$imgSrc."jpg";
                ?>

                <div class="col-md-3 col-sm-6 hero-feature">

                <div class="thumbnail" style="height:300px">
                    <img src=<?php echo $imgSrc?> width="100" height="100" alt="ALT NAME" class="img-responsive" />
                    <div class="caption" style="position:relative;height:150px">
                        <!-- <h3>{{$entry->original_filename}}</h3> -->
                        <?php $fileName = $entry->original_filename; ?>
                            @if(strlen($fileName) > 30)
                            <p>{{substr($fileName,0,30)."..." }}</p>
                            @else
                            <p>{{$fileName}}</p>
                            @endif
                        <p>Category: <?php echo $entry->category?></p>
                        <p style="position:absolute; left:30px"><font style="font-size:25px;color:#34495E">S$<?php echo $price?></font></p>

                        <form action=<?php echo url('shoppingcart/addtolibrary');?> method="post" style="position:absolute;right:120px;top:75px;border:none">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fid" value=<?php echo $fid?>>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <span class="glyphicon glyphicon-lock"></span>
                            </button>
                        </form>

                        <form action=<?php echo url('shoppingcart/add');?> method="post" style="position:absolute;right:120px;top:95px">
                            <input type="hidden" name="uid" value=<?php echo Auth::user()->id;?>>
                            <input type="hidden" name="fid" value=<?php echo $fid?>>
                            <button type="submit" style="border:none;background-color: Transparent">
                               <span class="glyphicon glyphicon-shopping-cart"></span>
                            </button>
                        </form>
                        <!-- <a href="#" style="position:absolute;right:120px;top:95px">
                                <span id="shoppingcartSpan" onclick="addToShoppingCart()" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                        </a> -->

                        <!-- <a href=<?php echo url('shoppingcart/add?uid='+Auth::user()->id+'&fid='+$fid) ?> style="position:absolute;right:120px;top:70px">
                                <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                        </a> -->
                        <p style="position:absolute; right:20px">
                            <button  class="btn btn-three" style="height:38px">
                                More Info
                            </button>
                        </p>
                    </div>
                </div>

                </div>
            @endforeach

        </div>
        <!-- /.row -->

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Your Website 2014</p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->

   


@endsection


