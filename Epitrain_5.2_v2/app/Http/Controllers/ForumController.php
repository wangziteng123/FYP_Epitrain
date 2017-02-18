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


    public function createDiscussion(Request $request) {
    	$user_id = \Auth::user()->id;
    	$category_id = $request->get('category');
    	$title= $request->get('title');
    	$description = $request->get('description');

    	$mytime = Carbon::now() ->timezone(\Config::get('app.timezone'));

    	DB::table('forumdiscussion') ->insert(
                ['user_id' => $user_id, 'category_id' => $category_id, 'title' => $title, 'description' => $description, 'created_at' => $mytime->toDateTimeString()]
        );

        return redirect()->route('forum');
    }

    public function toPage(Request $request) {
    	if($request->get('id')==null) {
    		$discussionId = 23;
    		return \View::make('forum.forumpage')->with('discussionId',$discussionId);
    	} else {
    		$discussionId = $request->get('id');
    		return \View::make('forum.forumpage')->with('discussionId',$discussionId);
    	}

    }
    public function showAllResponse(Request $request) {
        	if($request->get('id')==null) {
        		$discussionId = 23;
        		return \View::make('forum.forumResponsePage')->with('discussionId',$discussionId);
        	} else {
        		$discussionId = $request->get('id');
        		return \View::make('forum.forumResponsePage')->with('discussionId',$discussionId);
        	}

    }

    public function createResponse(Request $request) {
    	$user_id = $request->get('user_id');
    	$discussion_id = $request->get('discussionId');
    	$content= $request->get('content');

    	$mytime = Carbon::now() ->timezone(\Config::get('app.timezone'));

    	DB::table('forumresponse') ->insert(
                ['user_id' => $user_id, 'discussion_id' => $discussion_id, 'content' => $content, 'created_at' => $mytime->toDateTimeString()]
        );

        //return redirect()->route('forumpage')->with('discussionId',$discussion_id);
        return \View::make('forum.forumpage')->with('discussionId',$discussion_id);
    }
}
