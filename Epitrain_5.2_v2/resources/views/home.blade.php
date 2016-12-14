@extends('layouts.app')

@section('content')


    <!-- Split button -->

   <!-- Page Content -->
    <div class="container">

        <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer" style="min-height: 300px;">
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
                    <!-- /.input-group -->
                </div>
            </p>
        </header>

        <hr>

        <!-- Title -->
        <div class="row">
            <div class="col-lg-12">
                <h3>Best Sellers</h3>
            </div>
        </div>
        <!-- /.row -->

        <!-- Page Features -->
        <div class="row text-center">


            @foreach($entries as $entry)
                <?php
                     $imgSrc = $entry->filename;
                     $pos = strpos($imgSrc, "pdf");
                     $imgSrc = substr($imgSrc, 0, $pos);
                     $imgSrc = "img/".$imgSrc."jpg";
                ?>

                <div class="col-md-3 col-sm-6 hero-feature" >

                <div class="thumbnail" style="height:300px">
                    <img src=<?php echo $imgSrc?> width="100" height="100" alt="ALT NAME" class="img-responsive" />
                    <div class="caption">
                        <!-- <h3>{{$entry->original_filename}}</h3> -->
                        <?php $fileName = $entry->original_filename; ?>
                    @if(strlen($fileName) > 30)
                    <p>{{substr($fileName,0,30)."..." }}</p>
                    @else
                    <p>{{$fileName}}</p>
                    @endif
                        <p>This is the description of the ebook.</p>
                        <p>
                            <a href="#" class="btn btn-primary">Buy Now!</a> <a href="#" class="btn btn-default">More Info</a>
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


