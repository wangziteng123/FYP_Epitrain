<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Shoppingcart;
use App\Fileentry;
use App\Payment;

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
 
        return redirect()->back();
    }

    public function deleteOne(Request $request) {
       // $fileentry_id = $request->get('fid');
        //DB::table('shoppingcarts')->where('fileentry_id', '=', $fileentry_id)->delete();
        return redirect()->route('home');  
    }

    public function delete(Request $request) {
        $fileentry_id = $request->get('fid');
        $user_id = $request->get('uid');
        DB::table('shoppingcarts')
            ->where('fileentry_id', '=', $fileentry_id)
            ->where('user_id', '=', $user_id)
            ->delete();
        return redirect()->route('shoppingcart');  
    }


    public function addToLibraryOne(Request $request) {
        
        $user_id = $request->get('uid');
        $fileentry_id = $request->get('fid');

        $shoppingcartExist = \DB::table('shoppingcarts')
                        ->where('user_id', $user_id)
                        ->where('fileentry_id', $fileentry_id)
                        ->get();

        if(count($shoppingcartExist)) {
             DB::table('libraries') ->insert(
                ['fileentry_id' => $fileentry_id, 'user_id' => $user_id]
            );
             DB::table('shoppingcarts')
                ->where('fileentry_id', '=', $fileentry_id)
                ->where('user_id', '=',$user_id )
                ->delete();
        }

        DB::table('libraries') ->insert(
            ['fileentry_id' => $fileentry_id, 'user_id' => $user_id]
        );
 
 
        return view('mylibrary.index');
    }

// add books to library after payment
     public function addToLibrary(Request $request) {
        $user_id = $request->get('uid');

        $fidStr = $request->get('fidStr');

        $fidStrArray = explode(",", $fidStr);

        $sizeOfFidStrArray = count($fidStrArray);

        for($start = 0; $start < $sizeOfFidStrArray-1; $start++ ){

            $fileentry_id = $fidStrArray[$start +1];


            $shoppingcartExist = \DB::table('shoppingcarts')
                            ->where('user_id', $user_id)
                            ->where('fileentry_id', $fileentry_id)
                            ->get();

            if(count($shoppingcartExist)) {
                 DB::table('libraries') ->insert(
                    ['fileentry_id' => $fileentry_id, 'user_id' => $user_id]
                );
                 DB::table('shoppingcarts')
                    ->where('fileentry_id', '=', $fileentry_id)
                    ->where('user_id', '=',$user_id )
                    ->delete();
            }


         }

                return view('mylibrary.index');

    }

    public function checkout(Request $request) {
        $user_id = $request->get('uid');
        $count = $request->get('count');

        for($i = 1; $i <= $count; $i++) {
            $fid = $request->get("fid".$i);
            
            DB::table('libraries') ->insert(
                ['fileentry_id' => $fid, 'user_id' => $user_id]
            );
            DB::table('shoppingcarts')
                ->where('fileentry_id', '=', $fid)
                ->where('user_id', '=',$user_id )
                ->delete();
        }
        return view('mylibrary.index');
    }

    public function test() {
        return view('mylibrary.index');
    }
}
