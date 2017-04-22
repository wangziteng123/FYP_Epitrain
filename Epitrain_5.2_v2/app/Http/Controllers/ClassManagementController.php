<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * ClassManagementController Class used for manage the classes and change their activation status
 */
class ClassManagementController extends Controller
{
	/**
	 * index function get the id and category of a class
	 *
	 * @return view of existing course with the categories
	 */
    public function index() {
        //activates or deactivates users in courses
        $classData = DB::table('enrolment')
            ->join('course', 'enrolment.courseID', '=', 'course.courseID')
            ->selectRaw('enrolment.id as id, enrolment.isActive as enrolment_status, course.isActive as course_status')
            ->get();

        foreach($classData as $rowData) {
            if ($rowData->enrolment_status != $rowData->course_status) {
                DB::table('enrolment')
                    ->where('id', $rowData->id)
                    ->update(['isActive' => $rowData->course_status]);   
            }
        }

        //retrieve course and categories data for index page
    	$courseList = \DB::table('course')->get();
        $categories = \DB::table('category')->get();

        return view('classmanagement.index', compact('courseList','categories'));
    }

	/**
	 * enrolment function get list of enrolled students for classes
	 *
	 * @return view of existing course with the enrolled students
	 */
    public function enrolment() {

        $courseList = \DB::table('course')->get();
        $students = User::where('subscribe','=','0')
            ->where('isAdmin','=','0')
            ->orderBy('name', 'asc')
            ->get();
        $enrolmentList = \DB::table('enrolment')->get();

        return view('classmanagement.enrolment', compact('courseList','students','enrolmentList'));
    }

	/**
	 * courseMaterials function get list of materials for classes
	 *
	 * @return view of existing course with the materials of the class
	 */
    public function courseMaterials() {

        $courseList = \DB::table('course')->get();
        $materialList = \DB::table('courseMaterial')->get();
        $fileentries = \DB::table('fileentries')
            ->orderBy('original_filename', 'asc')
            ->get();

        return view('classmanagement.courseMaterials', compact('courseList','materialList','fileentries'));
    }

	/**
	 * filterStudents function check the subscription and enrolment status of a single student
	 *
	 * @param Request $request which takes in the name or email of a single student
	 * @return view of all the courses the student is currently enrolled
	 */
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
                ->orderBy('name', 'asc')
                ->get();
        //else, assume it's name
        } else {
            $students = User::where('subscribe','=','0')
                ->where('isAdmin','=','0')
                ->where('name','like','%'.$filterInput.'%')
                ->orderBy('name', 'asc')
                ->get();
        }

        return view('classmanagement.enrolment', compact('courseList','students','enrolmentList'));
    }

	/**
	 * filterEbooks function check the ebooks of one single course
	 *
	 * @param Request $request which takes in the course id of a single course
	 * @return view of all the materials of a single book
	 */
    public function filterEbooks(Request $request) {
        $filterInput = $request->input('courseIDInput');

        $courseList = \DB::table('course')->get();
        $materialList = \DB::table('courseMaterial')->get();
        $fileentries = \DB::table('fileentries')
            ->where('original_filename','like','%'.$filterInput.'%')
            ->orderBy('original_filename', 'asc')
            ->get();

        return view('classmanagement.courseMaterials', compact('courseList','materialList','fileentries'));
    }

	/**
	 * filterCourse function search for courses by details and check for their details and status
	 *
	 * @param Request $request which takes in the details admin entered for filtering
	 * @return view of the list of courses which match the criteria admin entered
	 */
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

	/**
	 * filterEnrolment function search for details and status for all the enrolment of students and courses
	 *
	 * @param Request $request which takes in the details admin entered for filtering
	 * @return view of the list of students and courses which match the filtering criteria admin entered
	 */
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
        $students = User::where('subscribe','=','0')
            ->where('isAdmin','=','0')
            ->orderBy('name', 'asc')
            ->get();
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

	/**
	 * filterCourseMaterials function search for the materials of a course
	 *
	 * @param Request $request which takes in the details of a course id and materials enrolled
	 * @return view of the list of courses and materials of the courses which match the filtering criteria admin entered
	 */
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
        $fileentries = \DB::table('fileentries')
            ->orderBy('original_filename', 'asc')
            ->get();

        $materialList = \DB::table('courseMaterial')
            ->join('fileentries', function ($join) use ($materialsInput, $courseIDInput) {
                $join->on('fileentries.id', '=', 'courseMaterial.fileEntriesID')
                     ->where('fileentries.original_filename', 'like', '%'.$materialsInput.'%')
                     ->where('courseMaterial.courseID','like','%'.$courseIDInput.'%');
                })
                ->get();

        return view('classmanagement.courseMaterials', compact('courseList','materialList','fileentries'));
    }

	/**
	 * addCourse function add a new course into the database with details of the course
	 *
	 * @param Request $request which takes in the details of a new course to add
	 * @return view of success or error message of the adding of new course
	 */
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
            'startDate' => 'required|date|after:yesterday',
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
        
        return redirect('classmanagement')->with('success','Course was added successfully!');
    }

	/**
	 * editCourse function edit the details of a course
	 *
	 * @param Request $request which takes in the new details of a course to modify
	 * @return view of success or error message of the adding of new course
	 */
    public function editCourse(Request $request) {
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
        //exit(var_dump($courseID));

        $this->validate($request, [
            'courseID' => 'max:64',
            'courseName' => 'max:128',
            'startDate' => 'date|after:yesterday',
            'endDate' => 'date|after:startDate',
        ]);
        if ($isActive == null) {
            DB::table('course')
                ->where('courseID', '=', $courseID)
                ->update(
                ['courseName' => $courseName, 
                 'courseArea' => $courseArea, 
                 'startDate' => $startDate, 
                 'endDate' => $endDate
                 ]
            );
        } else {
            DB::table('course')
                ->where('courseID', '=', $courseID)
                ->update(
                ['courseName' => $courseName, 
                 'courseArea' => $courseArea, 
                 'startDate' => $startDate, 
                 'endDate' => $endDate,
                 'isActive' => '1'
                 ]
            );
        }
        
        return redirect('classmanagement')->with('success','Course was updated successfully!');;
    }

	/**
	 * deleteCourse function add a new course into the database with id of the course
	 *
	 * @param Request $request which takes in the id of a course to delete
	 * @return view of success or error message of the deleting of course
	 */
    public function deleteCourse(Request $request) {
        $id = $request->input('id');

        DB::table('course')
        ->where('courseID', '=', $id)
        ->delete();
        
        return redirect('classmanagement')->with('success','Course was deleted successfully!');;
    }

	/**
	 * activateCourse function add activate course which was not activated
	 *
	 * @param Request $request which takes in the id of a course to delete
	 * @return view of success or error message for activating the course
	 */
    public function activateCourse(Request $request) {
        $id = $request->input('id');
        $status = $request->input('status');

        if ($status == 0) {
            DB::table('course')
            ->where('courseID', '=', $id)
            ->update(['isActive' => 1]);
        } else {
            DB::table('course')
            ->where('courseID', '=', $id)
            ->update(['isActive' => 0]);
        }
        
        return redirect('classmanagement')->with('success','Course was activated successfully!');;
    }

	/**
	 * addEnrolment function add one student to enroll in a course
	 *
	 * @param Request $request which takes in the id of a course or the list of students to enroll in the course
	 * @return view of success or error message for new enrolment of the course
	 */
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
        
        return redirect('enrolment')->with('success','Students were successfully added to course!');;
    }

	/**
	 * activateEnrolment function activate one or a list of students enrolled in a course
	 *
	 * @param Request $request which takes in the id of a course or the list of students enrolled in the course
	 * @return view of success or error message for activated enrolment of the course
	 */
    public function activateEnrolment(Request $request) {
        $id = $request->input('id');
        $status = $request->input('status');

        if ($status == 0) {
            DB::table('enrolment')
            ->where('id', '=', $id)
            ->update(['isActive' => 1]);
        } else {
            DB::table('enrolment')
            ->where('id', '=', $id)
            ->update(['isActive' => 0]);
        }
        
        return redirect('enrolment')->with('success','Student enrolment were successfully activated!');;
    }

	/**
	 * deleteEnrolment function delete one student enrolled in a course
	 *
	 * @param Request $request which takes in the id of a course or the list of students to enroll in the course
	 * @return view of success or error message for deleting enrolment of the course
	 */
    public function deleteEnrolment(Request $request) {
        $id = $request->input('id');

        DB::table('enrolment')
        ->where('id', '=', $id)
        ->delete();
        
        return redirect('enrolment')->with('success','Student enrolment were successfully removed!');
    }

	/**
	 * deleteEnrolments function delete a list of students enrolled in a course
	 *
	 * @param Request $request which takes in the id of a course or the list of students to enroll in the course
	 * @return view of success or error message for deleting enrolment of the course
	 */
    public function deleteEnrolments(Request $request) {
        $ids = $request->input('enrolment');

        if(empty($ids)) {

        } else {
                foreach($ids as $id) {
                    DB::table('enrolment')
                    ->where('id', '=', $id)
                    ->delete();
                }
        }


        return redirect('viewAllUsers')->with('success','Student enrolment were successfully removed!');
    }

	/**
	 * addEnrolment function add a list of students to enroll in a course
	 *
	 * @param Request $request which takes in the id of a course or the list of students to enroll in the course
	 * @return view of success or error message for new enrolment of the course
	 */
    public function addEnrolments(Request $request) {        
        $courseIDs = $request->input('courseID');
        $userID = $request->input('userId');

        $this->validate($request, [
            'courseID' => 'required|max:64',
        ]);
        
        if(empty($courseIDs)) {

        } else {
                foreach($courseIDs as $courseID) {
                    DB::table('enrolment')
                   ->insert(
                        ['courseID' => $courseID, 'userID' => $userID, 'isActive' => '1']
                    );
                }
        }

        
        return redirect('viewAllUsers')->with('success','Students were successfully added to course!');
    }

	/**
	 * filterStudentsForViewAllUsers function filter students by name or email and check for the student's enrolment status
	 *
	 * @param Request $request which takes in the name/email of the students
	 * @return view of all the enrolment of the student
	 */
    public function filterStudentsForViewAllUsers(Request $request) {
        $filterInput = $request->input('studentInput');
        $users = User::paginate(15);

        // $courseList = \DB::table('course')->get();
        // $enrolmentList = \DB::table('enrolment')->get();
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

        return view('usermanage.viewAllUsers', compact('users','students'));
    }

	/**
	 * addMaterial function add an existing material into an existing course
	 *
	 * @param Request $request which takes in the material list and a course id to add in materials
	 * @return view of success or error message of adding of books to a course
	 */
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
        
        return redirect('courseMaterials')->with('success','Course materials were successfully added!');
    }

	/**
	 * deleteMaterial function delete an existing material from an existing course
	 *
	 * @param Request $request which takes in the material list and a course id to delete materials
	 * @return view of success or error message of deleting of books from a course
	 */
    public function deleteMaterial(Request $request) {
        $id = $request->input('id');

        DB::table('courseMaterial')
        ->where('id', '=', $id)
        ->delete();
        
        return redirect('courseMaterials')>with('success','Course materials were successfully deleted!');
    }
    
}
