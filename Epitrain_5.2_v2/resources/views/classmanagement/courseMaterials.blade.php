@extends('layouts.app')

@section('content')
<?php
  use App\User;
?>
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Class Management</li>
        </ul>
    </div>
</div>

<?php
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $isFilter = true;
    if(strpos($url, "filter") == false) {
        $isFilter = false;
    }
?>

<ul class="nav nav-pills">
  <li><a href='/classmanagement'>Manage Courses</a></li>
  <li><a href=<?php echo URL::route('enrolment');?>>Manage Enrolment</a></li>
  <li class="active"><a href="javascript:void(0)">Manage Course Materials</a></li>
</ul>
@if (count($errors) > 0)
    <br/>
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            {{ $error }}
        @endforeach
    </div>
@endif
@if(Session::has('success'))
    <br/>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:18px">{{ Session::get('success') }}</p>
    </div>
@endif
 <div class="container" style="positon:relative;top:300px">
 	
	 <div id="page-wrapper" style="margin:10px">
        <div class ="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i>Manage Course Materials 
                        </div>
                        <div class="panel-body">
                            <div class="row">
                              <table class="table">
                                <legend>Filter materials</legend>
                                 <div class="row">Enter materials' name/course ID to filter them</div>
                                <form action=<?php echo URL::route('filterCourseMaterials');?> method="post" class="form-horizontal">
                                    <td>
                                        <div class="form-group">
                                          <div class="col-sm-12 col-xs-12">
                                            <input type="text" class="form-control" id="courseIDInput" name="courseIDInput" placeholder="Course ID">
                                          </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                          <div class="col-sm-12 col-xs-12">
                                            <input type="text" class="form-control" id="materialsInput" name="materialsInput" placeholder="Ebook name">
                                          </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                          <div class="col-sm-1 col-xs-1">
                                            <input type="submit" class="btn btn-info btn-raised" value="Search" style="background-color: #014667; color: #fff"></button>
                                          </div>
                                        </div>
                                    </td>
                                </form>
                              </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Course ID</th>
                                                    <th class="text-center">Materials</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($materialList as $material)
                                                    <?php
                                                        $fileName = \DB::table('fileentries')
                                                            ->where('id','=',$material->fileEntriesID)
                                                            ->value('original_filename');;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $material->courseID;?></td>
                                                        <td><?php echo $fileName;?></td>
                                                        <td>
                                                            <form action=<?php echo URL::route('deleteMaterial');?> method="post" >
                                                              <input type="hidden" name="id" value=<?php echo $material->id;?>>
                                                              <button class="btn btn-warning btn-raised btn-sm">
                                                                <i class="fa fa-window-close" aria-hidden="true"></i>
                                                              </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                        <!--<button class="btn btn-success btn-raised">Add a plan</button>-->
                    </div>
                    <!--End simple table example -->
            </div>
         <div>
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                       <div class="panel-heading" data-toggle="collapse" data-target="#demo" id="tblStatus">
                        Add materials to a course <span class="glyphicon glyphicon-plus pull-right" style="font-size: 25px;"></span>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    @if ($isFilter == false)
                                      <div class="collapse" id="demo">
                                    @else
                                      <div class="collapse in" id="demo">
                                    @endif
                                        <form action=<?php echo URL::route('filterEbooks');?> method="post" class="form-horizontal">
                                            <div class="form-group">
                                              <label for="ebookInput" class="col-md-2 control-label">Name/<br>Email</label>

                                              <div class="col-sm-10 col-xs-10">
                                                <input type="text" class="form-control" id="ebookInput" name="ebookInput" placeholder="Ebook title">
                                            </div>
                                            <div class="form-group">
                                              <div class="col-sm-2 col-xs-2 col-xs-offset-2">
                                                    <input type="submit" class="btn btn-raised btn-info" value="Search" style="background-color: #014667; color: #fff">
                                                </div>
                                                </div>
                                            </div>
                                        </form>

                                        <form action=<?php echo URL::route('addMaterial');?> method="post" class="form-horizontal">
                                            <div class="form-group">
                                            <label for="selectMaterials" class="col-md-2 control-label"> Material <br/><br/> List</label>
                                            <div class="col-md-10" style="height:200px">
                                              <select id="selectMaterials" multiple="" class="form-control" name="materialList[]" required style="height:190px">
                                                @foreach($fileentries as $file)
                                                    <option value=<?php echo $file->id;?>><font color="black" size = "3"><?php echo $file->original_filename;?></font></option>
                                                @endforeach
                                              </select>
                                            </div>
                                          </div>

                                          <div class="form-group">
                                            <label for="courseID" class="col-md-2 control-label">Course <br/><br/> ID</label>
                                            <div class="col-md-10">
                                              <select name="courseID" id="courseID" style="font-size:14px" class="form-control" placeholder="Choose course ID">
                                                  @foreach($courseList as $course)
                                                      <option value=<?php echo $course->courseID;?>><font color="black" size = "3"><?php echo $course->courseID;?></font></option>
                                                  @endforeach
                                              </select>
                                            </div>
                                          </div>

                                          <input type="submit" class="btn btn-success btn-raised" value="Submit" style="background-color: #014667; color: #fff">
                                         </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!--End simple table example -->
                </div>
            </div>
         </div>
       </div>
  </div>
  
<script>
  $(document).on("hide.bs.collapse show.bs.collapse", ".collapse", function () {
        $('#tblStatus').find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus");
    });
</script>
@endsection