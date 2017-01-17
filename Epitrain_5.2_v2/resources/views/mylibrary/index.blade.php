@extends('layouts.app')

@section('content')

<?php
        $entries2 = \DB::table('libraries')
            ->where('user_id', Auth::user()->id)
            ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
            ->select('libraries.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
            ->get();

?>
<div class="col-lg-12" style="position:relative">

 <div class="col-lg-10" style="position:relative; left:90px">

                <!-- Blog Post -->

                <!-- Title -->
                <h1 style="position: absolute;left: 14px;">My Library</h1>
                <br/><br/><br/>
                <hr>
                        

            @foreach($entries2 as $entry)
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
</div>






@endsection