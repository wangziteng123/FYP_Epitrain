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
  <li><a href='/classmanagement'>Manage Courses</a></li>
  <li class="active"><a href="javascript:void(0)">Manage Enrolment</a></li>
  <li><a href=<?php echo URL::route('courseMaterials');?>>Manage Course Materials</a></li>
</ul>
@if (count($errors) > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            {{ $error }}
        @endforeach
    </div>
@endif

<!-- Manage students -->
<div class="container" style="positon:relative;top:300px">
     <div id="page-wrapper" style="margin:10px">
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
                                                <form action=<?php echo URL::route('filterEnrolment');?> method="post" class="form-horizontal">
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
                                                            <input type="text" class="form-control" id="studEmailInput" name="studEmailInput" placeholder="Student email">
                                                          </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                          <div class="col-sm-12 col-xs-12">
                                                            <input type="text" class="form-control" id="studNameInput" name="studNameInput" placeholder="Name">
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

                                                @foreach($enrolmentList as $enrolment)
                                                <?php
                                                    $enrolledStud = User::where('id','=',$enrolment->userID)->first();
                                                    if ($enrolledStud != null) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $enrolment->courseID;?></td>
                                                        <td><?php echo $enrolledStud->email;?></td>
                                                        <td><?php echo $enrolledStud->name;?></td>
                                                         <?php if ($enrolment->isActive == 0) { ?>
                                                            <td>Inactive</td>
                                                        <?php } else { ?>
                                                            <td>Active</td>
                                                        <?php } ;?>
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
                                        <form action=<?php echo URL::route('filterStudents');?> method="post" class="form-horizontal">
                                            <div class="form-group">
                                              <label for="studentInput" class="col-md-2 control-label">Name/<br>Email</label>

                                              <div class="col-sm-10 col-xs-10">
                                                <input type="text" class="form-control" id="studentInput" name="studentInput" placeholder="Student name or email">
                                            </div>
                                            <div class="form-group">
                                              <div class="col-sm-2 col-xs-2 col-xs-offset-2">
                                                    <input type="submit" class="btn btn-raised btn-info" value="Search">
                                                </div>
                                                </div>
                                            </div>
                                        </form>
                                        <form action=<?php echo URL::route('addEnrolment');?> method="post" class="form-horizontal">
                                      
                                          <div class="form-group">
                                            <label for="selectStudents" class="col-md-2 control-label"> Student <br/><br/> List</label>
                                            <div class="col-md-10" style="height:200px">
                                              <select id="selectStudents" multiple="" class="form-control" name="studentList[]" required style="height:190px">
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
        </div>
    </div>
@endsection