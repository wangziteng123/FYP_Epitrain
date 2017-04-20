<?php

namespace App\Http\Controllers;
use App\Fileentry;
use Auth;
use DB;
use App\Http\Requests;
use Illuminate\Http\Request;

/**
 * HomeController Class used for generating Home page
 */
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
        
        date_default_timezone_set('Asia/Singapore');
        //activates or deactivates course by checking starting/ending dates
        $courseData = DB::table('course')
            ->select('courseID','startDate','endDate','isActive')
            ->get();

        $today = date("Y-m-d");

        foreach($courseData as $rowData) {
            if ($rowData->startDate <= $today && $rowData->endDate >= $today && $rowData->isActive == 0) {
                DB::table('course')
                    ->where('courseID', $rowData->courseID)
                    ->update(['isActive' => 1]);   
            } else if ($rowData->endDate < $today && $rowData->isActive == 1) {
                DB::table('course')
                    ->where('courseID', $rowData->courseID)
                    ->update(['isActive' => 0]);  
            }
        }

        //remove books from user library once the expiry date reaches 
        $libraryData = DB::table('libraries')
            ->select('id','expired_at')
            ->get();

        $today = date("Y-m-d H:i:s");

        foreach($libraryData as $libData) {
            //exit(var_dump($libData->expired_at < $today).'  '.var_dump($libraryData));
            if ($libData->expired_at != null) {
                if ($libData->expired_at < $today) {

                    DB::table('libraries')
                        ->where('id', $libData->id)
                        ->delete();   
                }
            }
        }

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
