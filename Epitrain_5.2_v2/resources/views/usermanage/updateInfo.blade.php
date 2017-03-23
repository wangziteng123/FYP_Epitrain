@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Update Info</li>
        </ul>
    </div>
</div>
@if (count($errors) > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            {{ $error }}
        @endforeach
    </div>
@endif
<div class="container">
    @if (session()->has('flash_notification.message'))
       <div class="alert alert-{{ session('flash_notification.level') }}">
           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

           {!! session('flash_notification.message') !!}
       </div>
    @endif
    <div class="row">
        <div class="col-md-8 col-md-offset-2 col-sm-9 col-sm-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Update Info</h4></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="PUT" action="/users/<?php echo Auth::user()->id;?>">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label"><font color="black" size = "3">Name</font></label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" style="font-size:16px" value="{{Auth::user()->name}}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label"><font color="black" size = "3">E-Mail Address</font></label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" style="font-size:16px" value="{{Auth::user()->email}}" disabled>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>






  <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label"><font color="black" size = "3"> Current Password</font></label>

                            <div class="col-md-6">
                                <input id="currentPassword" type="password" class="form-control" name="currentPassword" style="font-size:16px" placeholder="Enter your current password to make any changes">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">


                                <div class="col-md-6">
                                    <input id="passwordCheck" type="hidden" class="form-control" name="passwordCheck" value="{{Auth::user()->password}}">

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>







                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label"><font color="black" size = "3"> New Password</font></label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" style="font-size:16px" placeholder="Enter a new password if you wish to change your password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label"><font color="black" size = "3">Confirm New Password</font></label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" style="font-size:16px" placeholder="Reenter the new password">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>




                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-raised btn-success">
                                    <i class="fa fa-btn fa-user"></i> <font color="white">Update</font>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection
