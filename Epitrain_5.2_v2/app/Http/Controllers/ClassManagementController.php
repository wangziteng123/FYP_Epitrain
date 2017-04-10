<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClassManagementController extends Controller
{
    public function index() {

    	$courseList = \DB::table('course')->get();
        $categories = \DB::table('category')->get();

        return view('classmanagement.index', compact('courseList','categories'));
    }

    public function enrolment() {

        $courseList = \DB::table('course')->get();
        $students = User::where('subscribe','=','0')->where('isAdmin','=','0')->get();
        $enrolmentList = \DB::table('enrolment')->get();

        return view('classmanagement.enrolment', compact('courseList','students','enrolmentList'));
    }

    public function courseMaterials() {

        $courseList = \DB::table('course')->get();
        $materialList = \DB::table('courseMaterial')->get();
        $fileentries = \DB::table('fileentries')->get();

        return view('classmanagement.courseMaterials', compact('courseList','materialList','fileentries'));
    }

    public function filterStudents(Request $request) {
        $filterInput = $request->input('studentInput');

        $courseList = \DB::table('course')->get();
        $enrolmentList = \DB::table('enrolment')->get();
        $students = null;

        //if admin entered something that contains @, assume it's email
        if (strpos($filterInput,'@') == true) {
            $students = User::where('subscribe','=','0')
                ->where('isAdmin','=','0')
                ->where('email','like','%'.$filterInput.'%')
                ->get();
        //else, assume it's name
        } else {
            $students = User::where('subscribe','=','0')
                ->where('isAdmin','=','0')
                ->where('name','like','%'.$filterInput.'%')
                ->get();
        }

        return view('classmanagement.enrolment', compact('courseList','students','enrolmentList'));
    }

    public function filterEbooks(Request $request) {
        $filterInput = $request->input('courseIDInput');

        $courseList = \DB::table('course')->get();
        $materialList = \DB::table('courseMaterial')->get();
        $fileentries = \DB::table('fileentries')
            ->where('original_filename','like','%'.$filterInput.'%')
            ->get();

        return view('classmanagement.courseMaterials', compact('courseList','materialList','fileentries'));
    }

    public function filterCourses(Request $request) {
        $courseIDInput = $request->input('courseIDInput');
        $courseNameInput = $request->input('courseNameInput');
        $courseAreaInput = $request->input('courseAreaInput');
        $startDateInput = $request->input('startDateInput');
        $endDateInput = $request->input('endDateInput');
        $statusInput = $request->input('statusInput');

        
        if ($courseIDInput == null) {
           $courseIDInput = '';
        }
        if ($courseNameInput == null) {
           $courseNameInput = '';
        }
        if ($courseAreaInput == null) {
          $courseAreaInput  = '';
        }
        if ($startDateInput == null) {
           $startDateInput = '';
        }
        if ($endDateInput == null) {
          $endDateInput  = '';
        }
        if ($statusInput == null) {
           $statusInput = '';
        }

        $categories = \DB::table('category')->get();

        if($startDateInput == '' && $endDateInput == '') {
            $courseList = \DB::table('course')
            ->where('courseID','like','%'.$courseIDInput.'%')
            ->where('courseName','like','%'.$courseNameInput.'%')
            ->where('courseArea','like','%'.$courseAreaInput.'%')
            ->whereBetween('startDate',[$startDateInput, $endDateInput])
            ->where('isActive','=', $statusInput)
            ->get();
        } else if($startDateInput == '') {
            $courseList = \DB::table('course')
            ->where('courseID','like','%'.$courseIDInput.'%')
            ->where('courseName','like','%'.$courseNameInput.'%')
            ->where('courseArea','like','%'.$courseAreaInput.'%')
            ->where('endDate','<=', $endDateInput)
            ->where('isActive','=', $statusInput)
            ->get();
        } else if($endDateInput == '') {
            $courseList = \DB::table('course')
            ->where('courseID','like','%'.$courseIDInput.'%')
            ->where('courseName','like','%'.$courseNameInput.'%')
            ->where('courseArea','like','%'.$courseAreaInput.'%')
            ->where('startDate','>=',$startDateInput)
            ->where('isActive','=', $statusInput)
            ->get();
        } else {
            $courseList = \DB::table('course')
            ->where('courseID','like','%'.$courseIDInput.'%')
            ->where('courseName','like','%'.$courseNameInput.'%')
            ->where('courseArea','like','%'.$courseAreaInput.'%')
            ->where('isActive','=', $statusInput)
            ->get();
        }
        
        return view('classmanagement.index', compact('courseList','categories'));
    }

    public function filterEnrolment(Request $request) {
        $courseIDInput = $request->input('courseIDInput');
        $studEmailInput = $request->input('studEmailInput');
        $studNameInput = $request->input('studNameInput');
        $statusInput = $request->input('statusInput');

        
        if ($courseIDInput == null) {
           $courseIDInput = '';
        }
        if ($studEmailInput == null) {
           $studEmailInput = '';
        }
        if ($studNameInput == null) {
          $studNameInput  = '';
        }
        if ($statusInput == null) {
           $statusInput = '';
        }
        $courseList = \DB::table('course')->get();
        $students = User::where('subscribe','=','0')->where('isAdmin','=','0')->get();
        $enrolmentList = \DB::table('enrolment')
            ->join('users', function ($join) use ($studEmailInput, $studNameInput, $courseIDInput, $statusInput) {
                $join->on('users.id', '=', 'enrolment.userID')
                     ->where('users.email', 'like', '%'.$studEmailInput.'%')
                     ->where('users.name','like','%'.$studNameInput.'%')
                     ->where('enrolment.courseID','like','%'.$courseIDInput.'%')
                     ->where('enrolment.isActive','like','%'.$statusInput.'%');
                })
                ->get();

        return view('classmanagement.enrolment', compact('courseList','students','enrolmentList'));
    }

    public function filterCourseMaterials(Request $request) {
        $courseIDInput = $request->input('courseIDInput');
        $materialsInput = $request->input('materialsInput');

        if ($courseIDInput == null) {
           $courseIDInput = '';
        }
        if ($materialsInput == null) {
           $materialsInput = '';
        }
        $courseList = \DB::table('course')->get();
        $materialList = \DB::table('courseMaterial')->get();
        $fileentries = \DB::table('fileentries')->get();

        $materialList = \DB::table('courseMaterial')
            ->join('fileentries', function ($join) use ($materialsInput, $courseIDInput) {
                $join->on('fileentries.id', '=', 'courseMaterial.fileEntriesID')
                     ->where('fileentries.original_filename', 'like', '%'.$materialsInput.'%')
                     ->where('courseMaterial.courseID','like','%'.$courseIDInput.'%');
                })
                ->get();

        return view('classmanagement.courseMaterials', compact('courseList','materialList','fileentries'));
    }

    public function addCourse(Request $request) {
        $courseList = \DB::table('course')->get();
        $categories = \DB::table('category')->get();
        
        $courseID = $request->input('courseID');
        $courseName = $request->input('courseName');
        $courseArea = $request->input('courseArea');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $isActive = $request->input('isActive');

        $messages = [
            'before_or_equal' => 'Course end date must be after course start date',
        ];

        /*Validator::extend('before_or_equal', function($attribute, $value, $parameters, $validator) {
            return strtotime($validator->getData()[$parameters[0]]) >= strtotime($value);
        });*/

        $this->validate($request, [
            'courseID' => 'required|max:64',
            'courseName' => 'required|max:128',
            'courseArea' => 'required',
            'startDate' => 'required|date|after:today',
            'endDate' => 'required|date|after:startDate',
        ]);
        if ($isActive == null) {
            DB::table('course')->insert(
                ['courseID' => $courseID, 
                 'courseName' => $courseName, 
                 'courseArea' => $courseArea, 
                 'startDate' => $startDate, 
                 'endDate' => $endDate
                 ]
            );
        } else {
            DB::table('course')->insert(
                ['courseID' => $courseID, 
                 'courseName' => $courseName, 
                 'courseArea' => $courseArea, 
                 'startDate' => $startDate, 
                 'endDate' => $endDate,
                 'isActive' => '1'
                 ]
            );
        }
        
        return redirect('classmanagement');
    }

    public function deleteCourse(Request $request) {
        $id = $request->input('id');

        DB::table('course')
        ->where('courseID', '=', $id)
        ->delete();
        
        return redirect('classmanagement');
    }

    public function addEnrolment(Request $request) {        
        $courseID = $request->input('courseID');
        $studentList = $request->input('studentList');
        $isActive = $request->input('isActive');

        $this->validate($request, [
            'courseID' => 'required|max:64',
            'studentList' => 'required',
        ]);
        if ($isActive == null) {
            foreach($studentList as $studentID) {
                DB::table('enrolment')->insert(
                    ['courseID' => $courseID, 
                     'userID' => $studentID
                     ]
                );
            }
        } else {
            foreach($studentList as $studentID) {
                DB::table('enrolment')->insert(
                    ['courseID' => $courseID, 
                     'userID' => $studentID,
                     'isActive' => '1'
                     ]
                );
            }
        }
        
        return redirect('enrolment');
    }

    public function deleteEnrolment(Request $request) {
        $id = $request->input('id');

        DB::table('enrolment')
        ->where('id', '=', $id)
        ->delete();
        
        return redirect('enrolment');
    }

    public function addMaterial(Request $request) {        
        $courseID = $request->input('courseID');
        $materialList = $request->input('materialList');
        $isActive = $request->input('isActive');

        $this->validate($request, [
            'courseID' => 'required|max:64',
            'materialList' => 'required',
        ]);
        if ($isActive == null) {
            foreach($materialList as $materialID) {
                DB::table('courseMaterial')->insert(
                    ['courseID' => $courseID, 
                     'fileEntriesID' => $materialID
                     ]
                );
            }
        } else {
            foreach($materialList as $studentID) {
                DB::table('courseMaterial')->insert(
                    ['courseID' => $courseID, 
                     'fileEntriesID' => $materialID,
                     'isActive' => $isActive
                     ]
                );
            }
        }
        
        return redirect('courseMaterials');
    }

    public function deleteMaterial(Request $request) {
        $id = $request->input('id');

        DB::table('courseMaterial')
        ->where('id', '=', $id)
        ->delete();
        
        return redirect('courseMaterials');
    }
    
}
