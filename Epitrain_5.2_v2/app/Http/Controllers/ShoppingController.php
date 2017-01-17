<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Shoppingcart;
use App\Fileentry;

class ShoppingController extends Controller
{
    public function index() {
    	return view('shoppingcart.index');
    }

    public function add(Request $request) {
    	$user_id = $request->get('uid');
    	$fileentry_id = $request->get('fid');

    	DB::table('shoppingcarts') ->insert(
		    ['fileentry_id' => $fileentry_id, 'user_id' => $user_id]
		);
 
        return redirect()->route('home');
    }

    public function deleteOne(Request $request) {
       // $fileentry_id = $request->get('fid');
        //DB::table('shoppingcarts')->where('fileentry_id', '=', $fileentry_id)->delete();
        return redirect()->route('home');  
    }

    public function delete(Request $request) {
        $fileentry_id = $request->get('fid');
        DB::table('shoppingcarts')->where('fileentry_id', '=', $fileentry_id)->delete();
        return redirect()->route('shoppingcart');  
    }

    public function addToLibrary(Request $request) {
        $user_id = $request->get('uid');
        $fileentry_id = $request->get('fid');

        DB::table('libraries') ->insert(
            ['fileentry_id' => $fileentry_id, 'user_id' => $user_id]
        );
 
        return redirect()->route('home');
    }

}
