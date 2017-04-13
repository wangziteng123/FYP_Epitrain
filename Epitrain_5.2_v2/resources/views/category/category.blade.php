@extends('layouts.app')

@section('content')


<div class="col-sm-12">
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Manage Ebook and Forum Categories</li>
        </ul>
    </div>
</div>
<?php 
  $categories = \DB::table('category') ->get();
?>
@if(Session::has('failure'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('failure') }}</p>
    </div>
@endif
@if(Session::has('empty'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('empty') }}</p>
    </div>
@endif
@if(Session::has('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('success') }}</p>
    </div>
@endif
 <div class="col-sm-10 col-sm-offset-1">
    <b><form method="post" id="addCategory" action=<?php echo URL::route('addCategory');?>>
      <legend>Add new category</legend>
          Category Name: <input type="text" name="categoryName" class="form-control" >
      <button type="submit" class="btn btn-raised btn-info" style="background-color: #01466f;">Add</button>
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
    <form class="form-horizontal" method="post" id="setForumCat" action=<?php echo URL::route('setForumCat');?>>
      <fieldset>
          <b><legend>Set forum categories</legend></b>
          <h5>Please choose the categories to be available for discussion forum:</h5>

          <div class="checkbox">
            @foreach($categories as $category)
                <label>
                  <?php 
                      $original_Cat = $category->categoryname;
                      $status = $category->shownInForumCat;
                      $catArr = explode(" ", $original_Cat);
                      $new_Cat = "";

                      foreach ($catArr as $component) {
                        $new_Cat .= $component."_";
                      }
                  ?>
                  <?php if ($status == 1) { ;?>
                    <input type="checkbox" name=<?php echo $new_Cat;?> checked><font color="black">
                  <?php } else { ;?>
                    <input type="checkbox" name=<?php echo $new_Cat;?>><font color="black">
                  <?php } ;?>
                  <?php echo '  '.$original_Cat;?></font>

                </label>
            @endforeach
          </div>

          <div class="form-group">
              <button type="submit" class="btn btn-raised" style="background-color: #01466f; color:white">Set forum category<div class="ripple-container"></div></button>
          </div>

      </fieldset>
    </form>
    <br/>
    <br/>
    <form class="form-horizontal" method="post" id="setEbookCat" action=<?php echo URL::route('setEbookCat');?>>
      <fieldset>
          <b> <legend>Set ebook categories</legend></b>
          <h5>Please choose the categories to be available for ebooks:</h5>

          <div class="checkbox">
            @foreach($categories as $category)
                <label>
                  <?php 
                      $original_Cat = $category->categoryname;
                      $status = $category->shownInEbookCat;
                      $catArr = explode(" ", $original_Cat);
                      $new_Cat = "";
                      
                      foreach ($catArr as $component) {
                        $new_Cat .= $component."_";
                      }
                    ?>
                  <?php if ($status == 1) { ;?>
                    <input type="checkbox" name=<?php echo $new_Cat;?> checked><font color="black">
                  <?php } else { ;?>
                    <input type="checkbox" name=<?php echo $new_Cat;?>><font color="black">
                  <?php } ;?>
                  <?php echo '  '.$original_Cat;?></font>

                </label>
            @endforeach
          </div>

          <div class="form-group">
              <button type="submit" class="btn btn-raised btn-info" style="background-color: #01466f; color:white">Set ebook category<div class="ripple-container"></div></button>
          </div>

      </fieldset>
    </form>
    <br/>
    <br/>
    <form class="form-horizontal" method="post" id="setEbookShortcut" action=<?php echo URL::route('setEbookShortcut');?>>
      <fieldset>
          <b><legend>Set user categories' dropdown</legend></b>
          <h5>Please choose the categories to be included in the dropdown list at user's home page:</h5>

          <div class="checkbox">
            @foreach($categories as $category)
                <label>
                  <?php 
                      $original_Cat = $category->categoryname;
                      $status = $category->shownInUserCategories;
                      $catArr = explode(" ", $original_Cat);
                      $new_Cat = "";
                      
                      foreach ($catArr as $component) {
                        $new_Cat .= $component."_";
                      }
                    ?>
                  <?php if ($status == 1) { ;?>
                    <input type="checkbox" name=<?php echo $new_Cat;?> checked><font color="black">
                  <?php } else { ;?>
                    <input type="checkbox" name=<?php echo $new_Cat;?>><font color="black">
                  <?php } ;?>
                  <?php echo '  '.$original_Cat;?></font>

                </label>
            @endforeach
          </div>

          <div class="form-group">
              <button type="submit" class="btn btn-raised" style="background-color: #01466f; color:white">Set user category shortcut<div class="ripple-container"></div></button>
          </div>

      </fieldset>
    </form>
 </div>
@endsection