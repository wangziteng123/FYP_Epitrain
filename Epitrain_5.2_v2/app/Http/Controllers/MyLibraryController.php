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
        //
				$user_id = Auth::user()->id;
				//$books = DB::table('libraries')->where('user_id', '=', $user_id)->select('book_id');
        //$entries = Fileentry::all();
				$entries = DB::table('fileentries')
										->join('libraries', 'fileentries.id', '=', 'libraries.book_id')
										->where('user_id', '=', $user_id)
										->get();
        return view('mylibrary.index', compact('entries'));
    }
}
