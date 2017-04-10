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
<ul class="nav nav-pills">
  <li class="active"><a href="javascript:void(0)">Manage Courses</a></li>
  <li><a href=<?php echo URL::route('enrolment');?>>Manage Enrolment</a></li>
  <li><a href=<?php echo URL::route('courseMaterials');?>>Manage Course Materials</a></li>
</ul>
@if (count($errors) > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            {{ $error }}
        @endforeach
    </div>
@endif

 <div class="container" style="positon:relative;top:300px">
	 <div id="page-wrapper" style="margin:20px">
	 	<div class="row">
		    <div class="col-md-8">
				<div class="panel panel-primary">
                    <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i>Manage Courses 
                        </div>
                        <div class="panel-body">
                            <div class="row">Search engine will return records between start date and end date inputs<br></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Course ID</th>
                                                    <th class="text-center">Course Name</th>
                                                    <th class="text-center">Course Area</th>
                                                    <th class="text-center">Start Date</th>
                                                    <th class="text-center">End Date</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <form action=<?php echo URL::route('filterCourses');?> method="post" class="form-horizontal">
                                                    <td>
                                                        <div class="form-group">
                                                          <div class="col-sm-12 col-xs-12">
                                                            <input type="text" class="form-control" id="courseIDInput" name="courseIDInput" placeholder="ID">
                                                          </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                          <div class="col-sm-12 col-xs-12">
                                                            <input type="text" class="form-control" id="courseNameInput" name="courseNameInput" placeholder="Course name">
                                                          </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                          <div class="col-sm-12 col-xs-12">
                                                            <input type="text" class="form-control" id="courseAreaInput" name="courseAreaInput" placeholder="Course Area">
                                                          </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                          <div class="col-sm-12 col-xs-12">
                                                            <input type="date" class="form-control" id="startDateInput" name="startDateInput">
                                                          </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                          <div class="col-sm-12 col-xs-12">
                                                            <input type="date" class="form-control" id="endDateInput" name="endDateInput">
                                                          </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-2">
                                                        <div class="form-group">
                                                          <div class="col-md-10">
                                                            <select id="statusInput" name="statusInput" class="form-control">
                                                              <option value="0">Inactive</option>
                                                              <option value="1">Active</option>
                                                            </select>
                                                          </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                          <div class="col-sm-1 col-xs-1">
                                                            <input type="submit" class="btn btn-sm btn-info btn-raised" value="Search"></button>
                                                          </div>
                                                        </div>
                                                    </td>
                                                </form>
                                            	@foreach($courseList as $course)
                                            		<tr>
                                            			<td><?php echo $course->courseID;?></td>
                                            			<td><?php echo $course->courseName;?></td>
                                                        <td><?php echo $course->courseArea;?></td>
                                                        <td><?php echo date('d/m/Y',strtotime($course->startDate));?></td>
                                                        <td><?php echo date('d/m/Y',strtotime($course->endDate));?></td>
                                                        <?php if ($course->isActive == 0) { ?>
                                                            <td>Inactive</td>
                                                        <?php } else { ?>
                                                            <td>Active</td>
                                                        <?php } ;?>
                                            			<td>
                                            				<form action=<?php echo URL::route('deleteCourse');?> method="post" >
			                                                  <input type="hidden" name="id" value=<?php echo $course->courseID;?>>
			                                                  <button class="btn btn-danger btn-raised btn-sm">
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
                        <!--<button class="btn btn-info btn-raised">Add a plan</button>-->
                    </div>
                    <!--End simple table example -->
         	</div>

         	<div class="col-md-4">
			   <div class="panel panel-primary">
                 <div class="panel-heading">
                    Add a Course
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <form action=<?php echo URL::route('addCourse');?> method="post" >
                                        Course ID:
            							  <input type="text" class="form-control" name="courseID" required>
            						       Course Name:
            							  <input type="text" class="form-control" name="courseName" required>
                                        Course Area:
                                            <select name="courseArea" style="font-size:14px" class="form-control" placeholder="Choose ebook category">
                                                @foreach($categories as $category)
                                                    <option value=<?php echo $category->categoryname;?>><font color="black" size = "3"><?php echo $category->categoryname;?></font></option>
                                                @endforeach
                                            </select>
                                            Start Date:
                                            <input type="date" class="form-control" id ="startDate" name="startDate" required>
                                            End Date:
                                            <input type="date" class="form-control" id ="endDate" name="endDate" required>
                                            <div class="checkbox">
                                              <label>
                                                  <input type="checkbox" name="isActive"><font color="black">  Activate course</font>
                                              </label>
                                            </div>
        							      <input type="submit" class="btn btn-info btn-raised" value="Submit">
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

<link rel="stylesheet" type="text/css" href="{{ url('datepicker/css/datepicker.css') }}">
<script src="{{ asset('datepicker/js/bootstrap-datepicker.js') }}"></script>

<script>
    $(document).ready(function () {
        var startDate = $('#startDate').datepicker({
            format: 'yyyy-mm-dd',
            viewMode: 'months',  
        });
        var endDate = $('#endDate').datepicker({
            format: 'yyyy-mm-dd',
            viewMode: 'months',  
        });
        var startDateInput = $('#startDateInput').datepicker({
            format: 'yyyy-mm-dd',
            viewMode: 'months',  
        });
        var endDateInput = $('#endDateInput').datepicker({
            format: 'yyyy-mm-dd',
            viewMode: 'months',  
        });
    });

    $(function () {   
        $(".datepicker").on("dp.change", function (e) {    
            $('.datepicker').datepicker("hide");
        });
    });
</script>

@endsection