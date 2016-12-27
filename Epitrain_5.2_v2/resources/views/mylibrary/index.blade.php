@extends('layouts.app')

@section('content')


<div class="container">

        <div class="row">

            <!-- Blog Post Content Column -->
            <div class="col-lg-8">

                <!-- Blog Post -->

                <!-- Title -->
                <h1>MyLibrary</h1>

     
                <hr>

                <!-- Date/Time -->
                <p><span class="glyphicon glyphicon-time"></span> Posted on August 24, 2013 at 9:00 PM</p>

                <hr>

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
                    <!-- <embed width="100%" height="100%" name="plugin" src="http://localhost:8000/fileentry/get/php73AF.tmp.pdf" type="application/pdf"> -->
                     
                    <div class="caption">
                       <!--  <h3>{{$entry->original_filename}}</h3> -->

                        <?php $fileName = $entry->original_filename; ?>
                    @if(strlen($fileName) > 30)
                    <p>{{substr($fileName,0,30)."..." }}</p>
                    @else
                    <p>{{$fileName}}</p>
                    @endif

                        <p>This is the description of the ebook.</p>
                        <p>
                            <a href="{{route('getviewer', $entry->filename)}}" class="btn btn-primary">View</a> 
                        </p>
                    </div>
                </div>

                </div>
            @endforeach

        </div>

                <hr>

                <!-- Post Content -->
                
                <hr>

                <!-- Blog Comments -->

                <!-- Comments Form -->
                <div class="well">
                    <h4>Leave a Comment:</h4>
                    <form role="form">
                        <div class="form-group">
                            <textarea class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>

                <hr>

                <!-- Posted Comments -->

               
            </div>

            <!-- Blog Sidebar Widgets Column -->
            <div class="col-md-4">

                <!-- Blog Search Well -->
                <div class="well">
                    <h4 style="color:black">MyLibrary Search</h4>
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

                <!-- Blog Categories Well -->
                <div class="well">
                    <h4 style="color:black">New Posts</h4>
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                
                            </ul>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>

                <!-- Side Widget Well -->
                <div class="well">
                    <h4 style="color:black">Board</h4>
                    <p style="color:black">This is Empty now.</p>
                </div>

            </div>

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
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->










@endsection