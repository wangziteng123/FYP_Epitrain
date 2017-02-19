@extends('layouts.app')

@section('content')

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
<h1>Contact Admin</h1>

<h4>Use this form if you have feedback for admin about the system or have trouble accessing your account.</h4>

<br/>
<br/>
{!! Form::open(array('route' => 'contact_store', 'class' => 'form')) !!}

<div class="form-group" style="width:90%; margin:auto">
    {!! Form::label('Your Name') !!}
    {!! Form::text('name', null, 
        array('required', 
              'class'=>'form-control', 
              'placeholder'=>'Your name')) !!}
</div>
<br/>
<div class="form-group" style="width:90%; margin:auto">
    {!! Form::label('Your E-mail Address') !!}
    {!! Form::text('email', null, 
        array('required', 
              'class'=>'form-control', 
              'placeholder'=>'Your e-mail address')) !!}
</div>
<br/>
<div class="form-group" style="width:90%; margin:auto">
    {!! Form::label('Your Message') !!}
    {!! Form::textarea('message', null, 
        array('required', 
              'class'=>'form-control', 
              'placeholder'=>'Your message')) !!}
</div>
<br/>
<div class="form-group">
    {!! Form::submit('Contact Us!', 
      array('class'=>'btn btn-primary')) !!}
</div>
{!! Form::close() !!}

@endsection