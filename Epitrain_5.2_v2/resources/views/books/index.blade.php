@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Add new book</div>

                <div class="panel-body">
                    <h1><font color="black">All Users</font></h1>
                </div>

                <div>
                    @if ($books->count())
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Book Name</font></th>
        <th>ISBN</th>
        <th>Author</th>
        <th>Profession</th>
        <th>Description</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($books as $book)
                <tr>
                    <td>{{ $book->name }}</td>
          <td>{{ $book->ISBN }}</td>
          <td>{{ $book->author }}</td>
          <td>{{ $book->profession }}</td>
          <td>{{ $book->description }}</td>
                    <td>{{ link_to_route('books.edit', 'Edit', array($book->resourceID), array('class' => 'btn btn-info')) }}</td>
                    <td>
          {{ Form::open(array('method' 
=> 'DELETE', 'route' => array('books.destroy', $book->resourceID))) }}                       
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
                </tr>
            @endforeach
              
        </tbody>
      
    </table>
@else
    There are no users
@endif


                </div>
            </div>
        </div>
    </div>
</div>
@endsection



<!--
@extends('layouts.app')
@section('main')

<h1>All Users</h1>
<p>{{ link_to_route('books.create', 'Add new book') }}</p>
++++++++++++++++++++++++++-------------------
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
                    <td>{{ $book->name }}</td>
          <td>+++++++++{{ $book->ISBN }}</td>
          <td>{{ $book->author }}</td>
          <td>{{ $book->profession }}</td>
          <td>{{ $book->description }}</td>
                    <td>{{ link_to_route('books.edit', 'Edit', array($book->resourceID), array('class' => 'btn btn-info')) }}</td>
                    <td>
          {{ Form::open(array('method' 
=> 'DELETE', 'route' => array('books.destroy', $book->resourceID))) }}                       
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
                </tr>
            @endforeach
              
        </tbody>
      
    </table>
@else
    There are no users
@endif

@stop