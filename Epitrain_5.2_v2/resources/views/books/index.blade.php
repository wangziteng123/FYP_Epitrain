@extends('layouts.app')
<link rel="stylesheet" href="{{ URL::asset('css/style.css') }}" />
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Epishop</div>

                <div class="panel-body">
                    <h1><font color="black">All Books</font></h1>
                </div>

                <div>
                    @if ($books->count())
										
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Book Name</th>
        <th>ISBN</th>
        <th>Author</th>
        <th>Profession</th>
        <th>Description</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($books as $book)
                <tr>
                    <td>{{ $book->original_filename }}</td>
          <td>{{ $book->ISBN }}</td>
          <td>{{ $book->author }}</td>
          <td>{{ $book->profession }}</td>
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



