<?php

namespace App\Http\Controllers;
use App\Fileentry;
use Auth;
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
        //$users = "many users";
        $entries = Fileentry::all();
        if (count($entries) > 0) {
            return view('home', compact('entries'));
        } else if (Auth::user()->isAdmin()) {
            flash('There are no books in the shop! Please upload at least one book.', 'danger');
            return redirect('fileentry');
        } else {
            flash('There are no books in the shop! Please inform the page admin', 'danger');
            return view('usermanage.updateInfo', compact('users'));
        }
        
       
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
