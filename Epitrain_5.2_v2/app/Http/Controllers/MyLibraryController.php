<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Fileentry;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MyLibraryController extends Controller
{
    public function index()
    {
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
    public function getViewer() 
    {
    	$pdfUrl = "";
    	return view('mylibrary.pdfreader', compact('pdfUrl'));
    }
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
