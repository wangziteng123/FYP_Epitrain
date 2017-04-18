<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Shoppingcart;
use App\Fileentry;
use App\Payment;

class ShoppingController extends Controller
{
    /**
    *direct to shopping cart view page
    *
    * @return void
    */
    public function index() {
        return view('shoppingcart.index');
    }
    /**
    *add books to shopping cart
    *
    *@param Request $request
    *
    * @return void
    */
    public function add(Request $request) {
        $user_id = $request->get('uid');
        $fileentry_id = $request->get('fid');

        DB::table('shoppingcarts') ->insert(
            ['fileentry_id' => $fileentry_id, 'user_id' => $user_id]
        );
 
        return redirect()->back();
    }
    /**
    *deprecated. Replaced by delete(Request $request)
    *
    *@param Request $request
    *
    * @return void
    */
    public function deleteOne(Request $request) {

        return redirect()->route('home');  
    }
    /**
    *delete book from shopping cart
    *
    *@param Request $request
    *
    * @return void
    */
    public function delete(Request $request) {
        $fileentry_id = $request->get('fid');
        $user_id = $request->get('uid');
        DB::table('shoppingcarts')
            ->where('fileentry_id', '=', $fileentry_id)
            ->where('user_id', '=', $user_id)
            ->delete();
        return redirect()->route('shoppingcart');  
    }

    /**
    *add one book to library
    *
    *@param Request $request
    *
    * @return void
    */
    public function addToLibraryOne(Request $request) {
        
        $user_id = $request->get('uid');
        $fileentry_id = $request->get('fid');

        $shoppingcartExist = \DB::table('shoppingcarts')
                        ->where('user_id', $user_id)
                        ->where('fileentry_id', $fileentry_id)
                        ->get();

        //check if user is a subscriber
        $isSubscribe = Auth::user()->subscribe;

        // check if this user is a student in a course that requires this book
          $coursesOfThisBook = \DB::table('courseMaterial')
          ->where('fileEntriesID', $fileentry_id)
          ->pluck('courseID');

          $coursesOfThisUser = \DB::table('enrolment')
          ->where('userID', $user_id)
          ->where('isActive','=',1)
          ->pluck('courseID');

          $isStudent = false;

          //for students only
          $studentCourseID = "";
          $lastCourseEndTime = "";

          foreach ($coursesOfThisBook as $course) {
              if(in_array($course, $coursesOfThisUser)) {
                 $isStudent = true;
                 $courseEndTime = \DB::table('course')
                      ->where('courseID', $course)
                      ->value('endDate');

                 if ($studentCourseID == "") {
                    $studentCourseID = $course;
                    $lastCourseEndTime = $courseEndTime." 23:59:59";

                 } else if ($lastCourseEndTime < $courseEndTime) {
                    $studentCourseID = $course;
                    $lastCourseEndTime = $courseEndTime." 23:59:59";
                 }
                 
              }
          }

        if ($isSubscribe) {
            $subscribeEndTime = \DB::table('subscription')
                                ->where('user_id',$user_id)
                                ->where('end_date','>', date("Y-m-d H:i:s"))
                                ->value('end_date');
            //exit(var_dump($subscribeEndTime));

            if(count($shoppingcartExist)) {
                 DB::table('shoppingcarts')
                    ->where('fileentry_id', '=', $fileentry_id)
                    ->where('user_id', '=',$user_id )
                    ->delete();
            }

            DB::table('libraries') ->insert(
                ['fileentry_id' => $fileentry_id, 'user_id' => $user_id, 'expired_at' => $subscribeEndTime]
            );
        } else if ($isStudent) {

            if(count($shoppingcartExist)) {
                 DB::table('shoppingcarts')
                    ->where('fileentry_id', '=', $fileentry_id)
                    ->where('user_id', '=',$user_id )
                    ->delete();
            }
            //$lastCourseEndTime = date("Y-m-d H:i:s",$lastCourseEndTime);

            DB::table('libraries') ->insert(
                ['fileentry_id' => $fileentry_id, 'user_id' => $user_id, 'expired_at' => $lastCourseEndTime]
            );

        } else {
            if(count($shoppingcartExist)) {
                 DB::table('shoppingcarts')
                    ->where('fileentry_id', '=', $fileentry_id)
                    ->where('user_id', '=',$user_id )
                    ->delete();
            }

            DB::table('libraries') ->insert(
                ['fileentry_id' => $fileentry_id, 'user_id' => $user_id]
            );
        }

 
        return redirect('mylibrary');
    }


    /**
    *add books to library after payment
    *
    *@param Request $request
    *
    * @return void
    */
     public function addToLibrary(Request $request) {
        $user_id = $request->get('uid');

        $fidStr = $request->get('fidStr');

        $fidStrArray = explode(",", $fidStr);

        $sizeOfFidStrArray = count($fidStrArray);

        //check if user is a subscriber
        $isSubscribe = Auth::user()->subscribe;

        for($start = 0; $start < $sizeOfFidStrArray-1; $start++ ){

        $fileentry_id = $fidStrArray[$start +1];

        // check if this user is a student in a course that requires this book
        $coursesOfThisBook = \DB::table('courseMaterial')
              ->where('fileEntriesID', $fileentry_id)
              ->pluck('courseID');

        $coursesOfThisUser = \DB::table('enrolment')
              ->where('userID', $user_id)
              ->where('isActive','=',1)
              ->pluck('courseID');

              $isStudent = false;

              //for students only
              $studentCourseID = "";
              $lastCourseEndTime = "";

              foreach ($coursesOfThisBook as $course) {
                  if(in_array($course, $coursesOfThisUser)) {
                     $isStudent = true;
                     $courseEndTime = \DB::table('course')
                          ->where('courseID', $course)
                          ->pluck('endDate');

                     if ($studentCourseID == "") {
                        $studentCourseID = $course;
                        $lastCourseEndTime = $courseEndTime;

                     } else if ($lastCourseEndTime < $courseEndTime) {
                        $studentCourseID = $course;
                        $lastCourseEndTime = $courseEndTime;
                     }
                     
                  }
              }

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

        return redirect('mylibrary');

    }
    /**
    *checkout books to from shopping cart
    *
    *@param Request $request
    *
    * @return void
    */
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
        return redirect('mylibrary');
    }
    /**
    *test
    *
    *@param Request $request
    *
    * @return void
    */
    public function test() {
        return redirect('mylibrary');
    }
}
