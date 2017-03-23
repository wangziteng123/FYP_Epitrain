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
    //var_dump($chosenCategory['cat']);

    $bookList = \DB::table('fileentries')
        ->where('category', $chosenCategory['cat'])
        ->get();
    
    //var_dump($bookList);
?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Epishop</div>

                <div class="panel-body">
                    <h1><font color="black">Books under <?php echo $chosenCategory['cat'];?> category</font></h1>
                </div>

                <div>
                    @if (sizeof($bookList) > 0)
										
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Book Name</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($bookList as $book)
                                    <tr>
                                        <td>{{ $book->original_filename }}</td>
                                        <td>{{ $book->category }}</td>
                                        <td>{{ $book->description }}</td>
                    					<td><a href="{{ URL::to('buy',$book->id) }}"><i class="fa fa-btn fa-sign-out"></i>Buy</a><td>
                                    </tr>
                                @endforeach
                                  
                            </tbody>
                          
                        </table>
                    @else
                        There are no books currently
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ URL::asset('css/style.css') }}" />
@endsection



