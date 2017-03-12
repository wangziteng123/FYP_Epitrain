<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use Carbon\Carbon;


use App\Http\Requests;
use App\Http\Controllers\Controller;

class ForumController extends Controller
{
    public function index() {

    	return view('forum.forum');
    }

    public function indexAdmin(){
        return view('forum.forumAdmin');
    }


    public function createDiscussion(Request $request) {
    	$user_id = \Auth::user()->id;
    	$category_id = $request->get('category');
    	$title= $request->get('title');
    	$description = $request->get('description');

    	$mytime = Carbon::now() ->timezone(\Config::get('app.timezone'));

    	DB::table('forumdiscussion') ->insert(
                ['user_id' => $user_id, 'category_id' => $category_id, 'title' => $title, 'description' => $description, 'created_at' => $mytime->toDateTimeString()]
        );
        if (\Auth::user()->isAdmin){
            return view('forum.forumAdmin');

        } else{
            return redirect()->route('forum');}
        }
    
    public function liked($discussionId, $userId){
        if($discussionId!=null){
                $discussionUserId = DB::table('discussionUserLike')
                -> where ('discussion_id', '=', $discussionId)
                -> where ('user_id', '=', $userId)
                -> get();
                
                if($discussionUserId == null){
                    DB::table('discussionUserLike') ->insert(
                            ['discussion_id' => $discussionId,'user_id' => $userId]
                    );
                } else {
                    DB::table('discussionUserLike')
                    -> where ('discussion_id', '=', $discussionId)
                    -> where ('user_id', '=', $userId)
                    -> delete();
                }
            if (\Auth::user()->isAdmin){
                return view('forum.forumAdmin');

            } else{
                return redirect()->route('forum');}
            }
        //return \View::make('forum.forum')->with('discussionId',$discussionId);
        }
    }

    public function toPage(Request $request) {
    	if($request->get('id')==null) {
    		$discussionId = 23;
    		return \View::make('forum.forumpage')->with('discussionId',$discussionId);
    	} else {
    		$discussionId = $request->get('id');
            $views = DB::table('forumdiscussion') -> where('id', $discussionId) -> get();
            ++$views;
            DB::table('forumdiscussion') ->where('id', $discussionId) -> 
                increment('views');
    		return \View::make('forum.forumpage')->with('discussionId',$discussionId);
    	}

    }
    public function showAllResponse(Request $request) {
        	if($request->get('id')==null) {
        		$discussionId = 23;
        		return \View::make('forum.forumResponsePage')->with('discussionId',$discussionId);
        	} else {
        		$discussionId = $request->get('id');
                $views = DB::table('forumdiscussion') -> where('id', $discussionId) -> get();
                ++$views;
                DB::table('forumdiscussion') ->where('id', $discussionId) -> 
                    increment('views');
        		return \View::make('forum.forumResponsePage')->with('discussionId',$discussionId);
        	}

    }

    public function createResponse(Request $request) {
    	$user_id = $request->get('user_id');
    	$discussion_id = $request->get('discussionId');
    	$content= $request->get('content');

    	$mytime = Carbon::now() ->timezone(\Config::get('app.timezone'));

    	DB::table('forumresponse')->insert(
                ['user_id' => $user_id, 'discussion_id' => $discussion_id, 'content' => $content, 'created_at' => $mytime->toDateTimeString()]
        );

        //return redirect()->route('forumpage')->with('discussionId',$discussion_id);
        return \View::make('forum.forumpage')->with('discussionId',$discussion_id);
    }

    public function deleteDiscussion(Request $request){
        $discussion_id = $request->get('discussionId');

        DB::table('forumdiscussion')->where('id','=', $discussion_id)->delete();

        return view('forum.forumAdmin');
    }
    public function closeDiscussion(Request $request){

         $discussion_id = $request->get('discussionId');
         DB::table('forumdiscussion')->where('id','=', $discussion_id)->update(['isOpen' => "1"]);


          return view('forum.forumAdmin');



    }
	
	public function addCategory(Request $request){
	    $error="";
	    try{
	        $categoryName = $request->get('categoryName');
                $allCategory = DB::table('forumcategory') -> get();
                    $noOfCategories = sizeof($allCategory);
                     $categoryID = $noOfCategories + 1;

            foreach($allCategory as $category){
                $categoryNamefromDB = $category->categoryname;
                if($categoryName == $categoryNamefromDB ){
                   $error = "failed";

                }


            }

            if ($error == ""){
                 DB::table('forumcategory')->insert(['id' => $categoryID, 'categoryname' => $categoryName]);

                 $error = "Category successfully added";
            }


	    }catch (\Exception $e){

	         $error = "failed";
             $data = array(
                'error'  => $error
             );



	    }finally{
	        if($error == "Category successfully added"){
                flash('Category added Successfully!', 'success');
            }


            else{
                flash('Category was not added!', 'danger');
            }
            return redirect('forumAdmin');


	    }


    }


	
	


}
