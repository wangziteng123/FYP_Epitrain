@extends('layouts.app')

@section('content')



<h1><font color='white'>Manage Library</h1>

    <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer" style="width:500px; margin:0 auto; min-height: 300px; border-radius: 10px;">
            <h3><font color="black">Upload New File</font></h3>
            <form action="fileentry/add" method="post" enctype="multipart/form-data" style="width: 400px; margin:0 auto;border: 0px solid white;">
                <div class="well" style="width:400px; margin:0 auto;">
                    <h4 style="color:black; float: left">Choose the file to upload</h4>
                    <div class="input-group">
                        <input type="file" name="filefield" value="{{ csrf_token() }}" style="color:black">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </span>
                    </div>
                    <!-- /.input-group -->
                </div>
            </form>
            
        </header>
    
    
    <!--<form action="fileentry/add" method="post" enctype="multipart/form-data" class="jumbotron hero-spacer" style="width: 400px; margin:0 auto;border: 0px solid white;">

        
        <label for="exampleInputFile"><br/><h3><font color="black">Upload New PDF File</font></h3></label>
        <hr/>
        <input type="file" name="filefield" value="{{ csrf_token() }}" style="">
         <hr/>
                                      
         <button type="submit" class="btn btn-primary">Upload</button>
    </form>-->

    <hr style="width:1250px;">
        <div class="row">
            <div class="col-lg-12">
                <h3>Library</h3>
            </div>
        </div>
        
<div class="row" style="margin:0 auto;">
    <ul class="thumbnails">
     @foreach($entries as $entry)
        <?php
                                                        $imgSrc = $entry->filename;
                                                        $pos = strpos($imgSrc, "pdf");
                                                        $imgSrc = substr($imgSrc, 0, $pos);
                                                        $imgSrc = "img/".$imgSrc."jpg";
                                                    ?>
         <div class="col-md-4">
            <div class="thumbnail">
                <img src=<?php echo $imgSrc?> width="100" height="100" alt="ALT NAME" class="img-responsive" />
                <div class="caption">

                    <?php $fileName = $entry->original_filename; ?>
                    @if(strlen($fileName) > 30)
                    <p>{{substr($fileName,0,30)."..." }}</p>
                    @else
                    <p>{{$fileName}}</p>
                    @endif
                    <a href="{{route('getentry', $entry->filename)}}">View</a><br/>
                    @if (Auth::user()->isAdmin)
                    {{ Form::open(array('method'
                    => 'DELETE', 'route' => array('deleteentry', $entry->filename))) }}
                    {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                    {{ Form::close() }}
                    @else

                    @endif
                </div>
            </div>
        </div>
    @endforeach
    </ul>
</div>





@endsection