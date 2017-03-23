@extends('layouts.app')

@section('content')

@if(Session::has('message'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('message') }}</p>
    </div>
@endif

@if (session()->has('flash_notification.message'))
       <div class="alert alert-{{ session('flash_notification.level') }}">
           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
           {!! session('flash_notification.message') !!}
       </div>
@endif

<div class="container">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 col-sm-offset-2 col-xs-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="font-size:18px"><strong>Login</strong></div>

                    <div class="panel-body" style="">
                        {{ Html::image('img/Epitrain_logo.png', 'Epitrain Logo', array('class'=>'epitrainLogo')) }}
                    </div>


                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label"><font color="black" size="3px">E-Mail Address</font></label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email"  style="font-size:18px" value="{{ old('email') }}" placeholder="Enter your email">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('warning'))
                            <div class="alert alert-warning">
                                {{ session('warning') }}
                            </div>
                        @endif

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label" ><font color="black" size="3px">Password</font></label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" style="font-size:18px" placeholder="Enter your password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <div class="checkbox">
                                    <label for="remember">
                                        <input type="checkbox" id="remember"> 
                                        <span style="color:black">Remember Me</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
													<!--LogIn button-->
                            <div class="col-sm-offset-3 col-xs-7 col-sm-6 col-md-offset-3 col-lg-offset-3 col-xs-offset-2">
                                <button type="submit" class="btn btn-raised" style="background-color: darkblue">
                                    <i class="fa fa-btn fa-sign-in img-responsive" style="color:white"></i> <font color="white" class = "small">Login</font>
                                </button>
                            </div>
													<!-- forgot password function-->
                            <div class="col-sm-offset-3 col-xs-7 col-sm-6 col-md-offset-3 col-lg-offset-3">
                                <a class="btn btn-link text-center" href="{{ url('/password/reset') }}" style="font-size:18px"><font color="black" class = "small">Forgot Your Password?</font></a>
                            </div>
                        </div>
												
                    </form>
                </div>
            </div>
        </div>
</div>


@endsection
