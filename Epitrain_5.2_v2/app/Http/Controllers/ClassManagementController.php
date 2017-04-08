<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClassManagementController extends Controller
{
    public function index() {
    	$courseList = \DB::table('course')->get();
        $categories = \DB::table('category')->get();
        $materialList = \DB::table('courseMaterial')->get();
        return view('classmanagement.index', compact('courseList','categories','materialList'));
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
    
}
