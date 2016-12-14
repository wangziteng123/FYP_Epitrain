@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Library</div>

                <div class="panel-body">
                    this is your library
                    @yield('main')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
