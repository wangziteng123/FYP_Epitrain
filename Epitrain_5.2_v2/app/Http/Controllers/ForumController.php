<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\Notifiable;
use App\Notifications\DiscussionResponsed;
use App\Notifications\ClosedDiscussion;
use App\Notifications\LikedDiscussion;
use Illuminate\Http\Request;
use DB;
use DateTime;
use Carbon\Carbon;
use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ForumController extends Controller
{
    use Notifiable;
    
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
        
        //exit($description);
        //Added from here
        $discussionId = DB::table('forumdiscussion')
                -> where ('created_at', '=', $mytime)
                -> where ('user_id', '=', $user_id)
                -> value('id');
        
        $hashtags= FALSE;  
        preg_match_all("/(#\w+)/u", $description, $matches);  
        if ($matches) {
            $hashtagsArray = array_count_values($matches[0]);
            $hashtags = array_keys($hashtagsArray);
        }
        
        $arrayOfTags = array(); 
        if(!empty($hashtags)){
            foreach($hashtags as $tagname){
                $toAddTag = false;
                foreach ($arrayOfTags as $values){
                    if ($tagname == $values){
                        $toAddTag = true;
                    }
                }
                
                if (!$toAddTag){
                    array_push($arrayOfTags, $hashtags);
                    $tagExistInForumTags = DB::table('forumtags') 
                            -> where ('forum_tag', '=', $tagname)
                            -> value('forum_tag');
                    $tagCountInForumTags = DB::table('forumtags')
                            -> where ('forum_tag', '=', $tagname)
                            -> value('count');
                    DB::table('forumtags_discussion') -> insert(
                        ['forum_tag' => $tagname, 'discussion_id'=>$discussionId]
                    );
                    if($tagExistInForumTags != null){
                        DB::table('forumtags')
                        -> where ('forum_tag', '=', $tagname)
                        -> update(['count' => $tagCountInForumTags+1]);
                    }
                    else{
                        DB::table('forumtags') -> insert(
                            ['forum_tag' => $tagname, 'count'=>1]
                        );
                    }
                }
            }
        }
        
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
                    $userWhoCreatedDisc = \DB::table('forumdiscussion') 
                        ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                        //->select('users.*','forumdiscussion.id')
                        ->where('forumdiscussion.id', '=', $discussionId)
                        ->value('users.id');
                    
                    $userWhoLiked = \DB::table('users') 
                        ->where('id', '=', $userId)
                        ->value('name');
                    
                    $user = User::find($userWhoCreatedDisc);
                    
                    //$forumpageUrl = current($url);
                    
                    $user->notify(new LikedDiscussion($userWhoLiked));
                    
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
                return redirect()->route('forum');
            }
        }
        //return \View::make('forum.forum')->with('discussionId',$discussionId);
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
        
        $userWhoCreatedDisc = \DB::table('forumdiscussion') 
            ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
            //->select('users.*','forumdiscussion.id')
            ->where('forumdiscussion.id', '=', $discussion_id)
            ->value('users.id');
        
        $user = User::find($userWhoCreatedDisc);
        
        $forumpageUrl = $request->get('forumpageUrl');
        
        $user->notify(new DiscussionResponsed($forumpageUrl));
        
        //\Notification::send($userWhoCreatedDisc, new DiscussionResponsed($forumpageUrl));
        
    	DB::table('forumresponse')->insert(
                ['user_id' => $user_id, 'discussion_id' => $discussion_id, 'content' => $content, 'created_at' => $mytime->toDateTimeString()]
        );
        
        $hashtags= FALSE;  
        preg_match_all("/(#\w+)/u", $content, $matches);  
        if ($matches) {
            $hashtagsArray = array_count_values($matches[0]);
            $hashtags = array_keys($hashtagsArray);
        }
        
        $arrayOfTags = array(); 
        if(!empty($hashtags)){
            foreach($hashtags as $tagname){
                $toAddTag = false;
                foreach ($arrayOfTags as $values){
                    if ($tagname == $values){
                        $toAddTag = true;
                    }
                }
                
                if (!$toAddTag){
                    array_push($arrayOfTags, $hashtags);
                    $tagExistInForumTags = DB::table('forumtags') 
                            -> where ('forum_tag', '=', $tagname)
                            -> value('forum_tag');
                    $tagCountInForumTags = DB::table('forumtags')
                            -> where ('forum_tag', '=', $tagname)
                            -> value('count');
                    if($tagExistInForumTags != null){
                        DB::table('forumtags')
                        -> where ('forum_tag', '=', $tagname)
                        -> update(['count' => $tagCountInForumTags+1]);
                    }
                    else{
                        DB::table('forumtags') -> insert(
                            ['forum_tag' => $tagname, 'count'=>1]
                        );
                    }
                    
                    $tagExistInFTD = DB::table('forumtags_discussion')
                            -> where ('forum_tag', '=', $tagname)
                            -> where ('discussion_id', '=', $discussion_id)
                            -> get();
                    if($tagExistInFTD == null){
                        DB::table('forumtags_discussion') -> insert(
                            ['forum_tag' => $tagname, 'discussion_id'=>$discussion_id]
                        );
                    }
                }
            }
        }

        //return redirect()->route('forumpage')->with('discussionId',$discussion_id);
        return \View::make('forum.forumpage')->with('discussionId',$discussion_id);
    }
    
    //Added this
    
    public function showTagPosts(Request $request){
        //return view('forum.forum');
            $forumTag = $request->get('id');
        //$forumTag = $this->route('id');
        //echo $forumTag;
        return view('forum.forumShowTagPosts', compact('forumTag'));
        //return \View::make('forum.forumShowTagPosts', compact('forumTag'));
        //return \View::make('forum.forumShowTagPosts')->with('forumTag',$forumTag);
    }
    
    //To here

    public function deleteDiscussion(Request $request){
        $discussion_id = $request->get('discussionId');

        //abort(404, "discussion id: " + $discussion_id);
        DB::table('forumdiscussion')->where('id','=', $discussion_id)->delete();

        return view('forum.forumAdmin');
    }
    public function closeDiscussion(Request $request){

         $discussion_id = $request->get('discussionId');
         
         //Added Here
         
         //Get the Discussion Creator's ID
        
        $userWhoCreatedDisc = \DB::table('forumdiscussion') 
            ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
            //->select('users.*','forumdiscussion.id')
            ->where('forumdiscussion.id', '=', $discussion_id)
            ->value('users.id');
        
        $user = User::find($userWhoCreatedDisc);
        
        $forumpageUrl = $request->get('forumpageUrl');
        
        $user->notify(new ClosedDiscussion($forumpageUrl));
         
         //To here
         
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
