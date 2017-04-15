@extends('layouts.app')

@section('content')


<div class="col-sm-12">
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Admin Page</li>
        </ul>
    </div>
</div>
<?php 
    $adminEmailAdd = \DB::table('adminemail')
            -> orderBy('email_id', 'DESC')
            -> first();
    $adminEmail = $adminEmailAdd->email;
    
    $sessionTime = \DB::table('sessiontime')
            -> orderBy('session_id', 'DESC')
            -> first();
    $sessionTimeout = $sessionTime->session_time;
?>

 <div class="col-sm-10 col-sm-offset-1">
    @if (!empty($success))
        <div class="alert alert-success">
            {{ $success }}
        </div>
    @endif
    @if (!empty($error))
        <div class="alert alert-warning">
            {{ $error }}
        </div>
    @endif
    <b><form method="post" id="changedAdminEmail" action=<?php echo URL::route('changeAdminEmail');?>>
      <legend>Change Admin Email</legend>
          <input type="text" name="adminEmail" class="form-control style="font-size:16px" value="<?php echo $adminEmail?>" >
      <button type="submit" class="btn btn-raised btn-info" style="background-color: #01466f;">Change</button>
    </form></b>
    <br/>
    <br/>
    @if (!empty($sessionSuccess))
        <div class="alert alert-success">
            {{ $sessionSuccess }}
        </div>
    @endif
    @if (!empty($sessionError))
        <div class="alert alert-warning">
            {{ $sessionError }}
        </div>
    @endif
    <b><form method="post" id="changedTiming" action=<?php echo URL::route('changeSessionTimeout');?>>
      <legend>Set Session Timeout (in minutes)</legend>
          <input type="text" name="sessionTime" class="form-control style="font-size:16px" value="<?php echo $sessionTimeout/60?>" >
      <button type="submit" class="btn btn-raised btn-info" style="background-color: #01466f;">Change</button>
    </form></b>
    <br/>
    <br/>
    
 </div>
@endsection