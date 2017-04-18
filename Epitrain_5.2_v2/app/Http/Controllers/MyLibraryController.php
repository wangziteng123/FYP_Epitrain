<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Fileentry;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * MyLibraryController Class used for my library function
 */
class MyLibraryController extends Controller
{
	/**
	* index function generate the page of my library 
	*
	* @return view of files with details within my library
	*/
    public function index()
    {
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

        // gather data for index files
		$user_id = Auth::user()->id;
        $mode = "original_filename-asc";
		$entries = DB::table('libraries')
			->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
            ->where('user_id', '=', $user_id)
            ->select('libraries.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
            ->orderBy('original_filename', 'asc')
			->paginate(12);

        return view('mylibrary.index', compact('entries','mode'));
    }
	
	/**
	* getViewer function generate ebook browsing page
	*
	* @return view of the content of the book
	*/
    public function getViewer() 
    {
    	$pdfUrl = "";
    	return view('mylibrary.pdfreader', compact('pdfUrl'));
    }
	
	/**
	* sort function sort the books in my library by name or category
	*
	* @param Request $request takes in the user id, sortField to sort books by name or category
	* 	sort by ascending or descending, and names of the books
	*
	* @return view of books in my library in sorted order
	*/
    public function sort(Request $request)
    {
        $user_id = Auth::user()->id;
        $sortField = $request->input('sortField');
        $mode = $request->input('mode');
        $entries = Fileentry::orderBy('original_filename', 'asc')->paginate(12);

        //exit(var_dump($sortField));

        if ($mode == null) {
            $mode = "original_filename-asc";
        }
        $modeArr = explode("-", $mode);
        if ($sortField == $modeArr[0] && $modeArr[1] == "asc") {
            //exit($sortField);
            $entries = DB::table('libraries')
                ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
                ->where('user_id', '=', $user_id)
                ->select('libraries.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
                ->orderBy($sortField, 'desc')->paginate(12);
            $mode = $sortField."-desc";

        } else if ($sortField == $modeArr[0] && $modeArr[1] == "desc") {
            $entries = DB::table('libraries')
                ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
                ->where('user_id', '=', $user_id)
                ->select('libraries.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
                ->orderBy($sortField, 'asc')->paginate(12);
            $mode = $sortField."-asc";

        } else {
            $entries = DB::table('libraries')
                ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
                ->where('user_id', '=', $user_id)
                ->select('libraries.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
                ->orderBy($sortField, 'asc')->paginate(12);
            $mode = $sortField."-asc";
        }
 
        return view('mylibrary.index', compact('entries','mode'));
    }
	
	/**
	* filterLibrary function filter the books in my library by category or book name
	*
	* @param Request $request takes in the category choosed, sortField to sort books by name or category, 
	* 	name of the ebook and ascending or descending order of the search results
	*
	* @return view of the books which meet search criteria
	*/
    public function filterLibrary(Request $request) {
        $user_id = Auth::user()->id;
        $category = $request->input('category');
        $sortField = $request->input('sortField');
        $ebookName = $request->input('ebookName');
        $mode = $request->input('mode');

        $catList = explode('_',$category);
        $correctCat = "";
        foreach($catList as $cat) {
            $correctCat .= $cat . " ";
        }
        $correctCat = substr($correctCat,0, -2);
        if ($category == null) {
           $category = '';
        }
        if ($ebookName == null) {
           $ebookName = '';
        }

        if ($mode == null) {
            $mode = "original_filename-asc";
        }

        $modeArr = explode("-", $mode);
        $order = $modeArr[1];
        //exit(var_dump($correctCat));
        $entries = DB::table('libraries')
                ->join('fileentries', 'libraries.fileentry_id', '=', 'fileentries.id')
                ->where('user_id', '=', $user_id)
                ->where('fileentries.original_filename','like','%'.$ebookName.'%')
                ->where('fileentries.category','like','%'.$correctCat.'%')
                ->select('libraries.*', 'fileentries.category', 'fileentries.price', 'fileentries.description','fileentries.original_filename','fileentries.id','fileentries.filename')
                ->orderBy($sortField, 'desc')->paginate(12);


        return view('mylibrary.index', compact('entries','mode'));
    }

}
