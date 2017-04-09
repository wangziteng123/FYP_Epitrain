@extends('layouts.app')

@section('content')
<?php
  use App\User;
?>
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">Manage Courses</li>
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

 <div class="container" style="positon:relative;top:300px">
 	
	 <div id="page-wrapper" style="margin:20px">
	 	<div class="row">
		    <div class="col-md-7">
				<div class="panel panel-primary">
                    <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i>Manage Courses 
                        </div>
                        <div class="panel-body">
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
                        <!--<button class="btn btn-success btn-raised">Add a plan</button>-->
                    </div>
                    <!--End simple table example -->
         	</div>

         	<div class="col-md-5">
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
                                            <option value=<?php echo $category->id;?>><font color="black" size = "3"><?php echo $category->categoryname;?></font></option>
                                        @endforeach
                                    </select>
                                    Start Date:
                                    <input type="date" class="form-control datepicker" name="startDate" required>
                                    End Date:
                                    <input type="date" class="form-control datepicker" name="endDate" required>
                                    <div class="checkbox">
                                      <label>
                                          <input type="checkbox" name="isActive"><font color="black">  Activate course</font>
                                      </label>
                                    </div>
							      <input type="submit" class="btn btn-success btn-raised" value="Submit">
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

<!-- Manage students -->
       <div class ="row">
            <div class="col-md-7">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i>Manage Student Enrolment
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Course ID</th>
                                                    <th class="text-center">Student Email</th>
                                                    <th class="text-center">Student Name</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($enrolmentList as $enrolment)
                                                <?php
                                                    $enrolledStud = User::where('id','=',$enrolment->userID)->first();
                                                    if ($enrolledStud != null) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $enrolment->courseID;?></td>
                                                        <td><?php echo $enrolledStud->email;?></td>
                                                        <td><?php echo $enrolledStud->name;?></td>
                                                        <td><?php echo $enrolment->isActive;?></td>
                                                        <td>
                                                            <form action=<?php echo URL::route('deleteEnrolment');?> method="post" >
                                                              <input type="hidden" name="id" value=<?php echo $enrolment->id;?>>
                                                              <button class="btn btn-warning btn-raised btn-sm">
                                                                <i class="fa fa-window-close" aria-hidden="true"></i>
                                                              </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
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

            <div class="col-md-5">
                <div class="panel panel-primary">
                       <div class="panel-heading">
                        Enroll students in courses
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <form action=<?php echo URL::route('addEnrolment');?> method="post" class="form-horizontal">
                                      
                                          <div class="form-group">
                                            <label for="selectStudents" class="col-md-2 control-label"> Student <br/><br/> List</label>
                                            <div class="col-md-10">
                                              <select id="selectStudents" multiple="" class="form-control" name="studentList[]">
                                                @foreach($students as $student)
                                                    <option value=<?php echo $student->id;?>><font color="black" size = "3"><?php echo $student->name.'('.$student->email.')';?></font></option>
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

                                          <div class="form-group">
                                            <div class="checkbox">
                                              <label>
                                                  <input type="checkbox" name="isActive"><font color="black">  Activate enrolment</font>
                                              </label>
                                            </div>
                                          </div>

                                        <input type="submit" class="btn btn-success btn-raised" value="Submit">
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
            <div class ="row">
            <div class="col-md-7">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i>Manage Course Materials 
                        </div>
                        <div class="panel-body">
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

            <div class="col-md-5">
                <div class="panel panel-primary">
                       <div class="panel-heading">
                        Add materials to a course
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <form action=<?php echo URL::route('addMaterial');?> method="post" class="form-horizontal">
                                            <div class="form-group">
                                            <label for="selectMaterials" class="col-md-2 control-label"> Material <br/><br/> List</label>
                                            <div class="col-md-10">
                                              <select id="selectMaterials" multiple="" class="form-control" name="materialList[]">
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

                                          <input type="submit" class="btn btn-success btn-raised" value="Submit">
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

<link rel="stylesheet" type="text/css" href="{{ url('datepicker/css/datepicker.css') }}">
<script src="{{ asset('datepicker/js/bootstrap-datepicker.js') }}"></script>

<script>
    $(document).ready(function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            viewMode: 'months',
        });
    });
</script>

@endsection