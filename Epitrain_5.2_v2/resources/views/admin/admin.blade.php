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
  $categories = \DB::table('category') ->get();
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
   <b> <form method="post" id="editCategory" action=<?php echo URL::route('editCategory');?>>
        <legend>Edit existing category</legend>
        <select name="category" required>
          @foreach($categories as $category)
            <a href="#"><font size="3"><?php echo $category->categoryname;?></font></a><br/>
              <option value=<?php echo $category->id;?> style=""><?php echo $category->categoryname;?></option>
          @endforeach
        </select>
        <br/><br/>
          New category Name: <input type="text" name="categoryName" class="form-control" >
      <input type="submit" value="Edit Category" class="btn btn-raised btn-info" style="background-color: #01466f;"></button>
    </form></b>
    <br/>
    <br/>
    
 </div>
@endsection