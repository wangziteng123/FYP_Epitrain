<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Auth;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LibraryController extends Controller
{
    //
		/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
		
		public function buy($book_id)
		{
				$user_id = Auth::user()->id;
				$current_time = Carbon::now()->toDayDateTimeString();
				$id = DB::table('libraries')->insertGetId(
						['user_id' => $user_id, 'book_id' => $book_id, 'created_at' => $current_time]
				);
				
				//echo $id;
				return redirect('mylibrary');
		}
}
