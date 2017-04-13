@extends('layouts.app')

@section('content')

@if(Session::has('failure'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('failure') }}</p>
    </div>
@endif
@if(Session::has('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('success') }}</p>
    </div>
@endif
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Create New User</li>
        </ul>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-9 col-sm-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Create New User</h4></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/store') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-sm-4 control-label"><font color="black" size = "3">Name</font></label>

                            <div class="col-sm-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" style="font-size:16px" placeholder="Enter new user's name" required>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-sm-4 control-label"><font color="black" size = "3">E-Mail Address</font></label>

                            <div class="col-sm-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" style="font-size:16px" placeholder="Enter email of the user" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-sm-4 control-label"><font color="black" size = "3">Password</font></label>

                            <div class="col-sm-6">
                                <input id="password" type="password" class="form-control" name="password" style="font-size:16px" placeholder="Create a temporary password for the new user" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-sm-4 control-label"><font color="black" size = "3">Confirm Password</font></label>

                            <div class="col-sm-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" style="font-size:16px" placeholder="Reenter the new password" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>-->
												
												<div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <div class="radio radio-primary">
                                   <label>
																			<input type="radio" name="make-admin" id="make-admin" value="0" checked="">
																			StandardUser
																		</label>
                                </div>
																<div class="radio radio-primary">
                                   <label>
																			<input type="radio" name="make-admin" id="make-admin" value="1">
																			Administrator
																		</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6 col-sm-offset-4">
                                <button type="submit" class="btn btn-raised btn-success" id="register" style="background-color: #01466f;">
                                    <i class="fa fa-btn fa-user"></i> <font color="white">Register</font>
                                </button>
                            </div>
                        </div>
                    </form>
                    <h5> <div id="createUserMesage"></div></h5>
                    <p hidden id="dom-target">
                    <?php //if( $data['error'] != null){
                        if(isset($error)){
                            echo $error;
                        }else{
                            $error ="";
                        }
                    ?></p>
                </div>
            </div>      
        </div>
    </div>
</div>

@endsection