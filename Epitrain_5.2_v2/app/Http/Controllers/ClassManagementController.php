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
        $materialList = \DB::table('courseMaterial')->get();
        $students = User::where('subscribe','=','0')->where('isAdmin','=','0')->get();
        $enrolmentList = \DB::table('enrolment')->get();
        $fileentries = \DB::table('fileentries')->get();

        return view('classmanagement.index', compact('courseList','categories','materialList','students','enrolmentList','fileentries'));
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
                 'isActive' => $isActive
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
                     'isActive' => $isActive
                     ]
                );
            }
        }
        
        return redirect('classmanagement');
    }

    public function deleteEnrolment(Request $request) {
        $id = $request->input('id');

        DB::table('enrolment')
        ->where('id', '=', $id)
        ->delete();
        
        return redirect('classmanagement');
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
        
        return redirect('classmanagement');
    }

    public function deleteMaterial(Request $request) {
        $id = $request->input('id');

        DB::table('enrolment')
        ->where('id', '=', $id)
        ->delete();
        
        return redirect('classmanagement');
    }
    
}
