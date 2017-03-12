@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Contact Admin</li>
        </ul>
    </div>
</div>
@if(Session::has('message'))
    <div class="alert alert-info">
      {{Session::get('message')}}
    </div>
@endif

@if(count($errors) > 0)
  <div class="alert alert-danger" style="width:90%; margin:auto">
      <h4>Please correct the following errors:</h4>
      @foreach($errors->all() as $error)
          {{ $error }}</br>
      @endforeach
    
  </div>
@endif
<div class="container">
  <div class ="col-sm-9 col-xs-9 col-xs-offset-1 col-sm-offset-1">
  <div class="panel panel-success">
    <div class="panel-heading" style="font-size:16px;color:white">
      <strong style="font-size:25px">Contact Admin</strong>

      <p>Use this form if you have feedback for admin about the system or have trouble accessing your account.</p>
    </div>
    <div class="panel-body">
        {!! Form::open(array('route' => 'contact_store', 'class' => 'form')) !!}

        <div class="form-group" style="width:90%; margin:auto" style="font-size:16px">
            {!! Form::label('name','Name',array('style="font-size:20px"')) !!}
            {!! Form::text('name', Auth::check() ? auth()->user()->name : null, 
                array('required', 
                      'class'=>'form-control', 
                      'placeholder'=>'Your name',
                      'style="font-size:16px"')) !!}
        </div>
        <br/>
        <div class="form-group" style="width:90%; margin:auto">
            {!! Form::label('email','E-mail Address',array('style="font-size:20px"')) !!}
            {!! Form::text('email', Auth::check() ? auth()->user()->email : null, 
                array('required', 
                      'class'=>'form-control', 
                      'placeholder'=>'Your e-mail address',
                      'style="font-size:16px"')) !!}
        </div>
        <br/>
        <div class="form-group" style="width:90%; margin:auto">
            {!! Form::label('message','Message',array('style="font-size:20px"')) !!}
            {!! Form::textarea('message', null, 
                array('required', 
                      'class'=>'form-control', 
                      'placeholder'=>'Your message',
                      'style="font-size:16px"')) !!}
        </div>
        <br/>
        <div class="form-group">
            {!! Form::submit('Contact Us!', 
              array('class'=>'btn btn-raised btn-info')) !!}
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection