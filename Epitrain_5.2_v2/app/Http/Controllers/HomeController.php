<?php

namespace App\Http\Controllers;
use App\Fileentry;

use App\Http\Requests;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = "many users";
        $entries = Fileentry::all();
 
        return view('home', compact('entries'));
       
    }
    public function create()
    {
        return view('usermanage.create');
    }
		
		public function shop()
    {
        $books = Fileentry::all();
 
        return view('books.index', compact('books'));
    }
}
