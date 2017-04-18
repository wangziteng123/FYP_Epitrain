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
    /**
    *direct admin user to the admin forum page
    *
    * @return void
    */
    public function indexAdmin(){
        $tagsForSearch = null;
        return view('forum.forumAdmin', compact('tagsForSearch'));
    }

    //Added Here
    /**
    *sorting of forum threads for date, category, likes, views
    *
    *@param Request $request
    *
    * @return void
    */
    public function dsort(Request $request){
        $sortField = $request->input('sortField');
        $oldValue = $request->input('oldValue');
        $count = $request->input('count');
        if($count == null){
            $count = 0;
        }
        
        if($sortField=="date"){
            if(($oldValue==$sortField && $count%2==0) || (empty($oldValue) && $count%2==0)){
                $discussions = \DB::table('forumdiscussion')
                                ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
                                ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                                ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
                                -> orderBy('created_at', 'DESC')
                                -> paginate(5);
                
                $oldValue = $sortField;
                $count = $count + 1;
            } else{
                $discussions = \DB::table('forumdiscussion')
                                ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
                                ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                                ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
                                -> orderBy('created_at', 'ASC')
                                -> paginate(5);
                $oldValue = $sortField;
                $count = 2;
            }
        } elseif($sortField=="category"){
            if(($oldValue==$sortField && $count%2==0) || (empty($oldValue) && $count%2==0)){
                $discussions = \DB::table('forumdiscussion')
                                ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
                                ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                                ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
                                -> orderBy('categoryname', 'ASC')
                                -> paginate(5);
                
                $oldValue = $sortField;
                $count = $count + 1;
            } else{
                $discussions = \DB::table('forumdiscussion')
                                ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
                                ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                                ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
                                -> orderBy('categoryname', 'DESC')
                                -> paginate(5);
                $oldValue = $sortField;
                $count = 2;
            }
        } elseif($sortField=="likes"){
            if(($oldValue==$sortField && $count%2==0) || (empty($oldValue) && $count%2==0)){
                $discussions = \DB::table('forumdiscussion')
                                ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
                                ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                                ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
                                -> orderBy('likes', 'DESC')
                                -> paginate(5);
                
                $oldValue = $sortField;
                $count = $count + 1;
            } else{
                $discussions = \DB::table('forumdiscussion')
                                ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
                                ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                                ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
                                -> orderBy('likes', 'ASC')
                                -> paginate(5);
                $oldValue = $sortField;
                $count = 2;
            }
        } elseif($sortField=="views"){
            if(($oldValue==$sortField && $count%2==0) || (empty($oldValue) && $count%2==0)){
                $discussions = \DB::table('forumdiscussion')
                                ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
                                ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                                ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
                                -> orderBy('views', 'DESC')
                                -> paginate(5);
                
                $oldValue = $sortField;
                $count = $count + 1;
            } else{
                $discussions = \DB::table('forumdiscussion')
                                ->join('category', 'forumdiscussion.category_id', '=', 'category.id')
                                ->join('users', 'forumdiscussion.user_id', '=', 'users.id')
                                ->select('forumdiscussion.*', 'category.categoryname', 'users.name')
                                -> orderBy('views', 'ASC')
                                -> paginate(5);
                $oldValue = $sortField;
                $count = 2;
            }
        }
        /*if(empty($sortField)){
            $sortField = "name";
        }*/
        //$entries = Fileentry::orderBy('original_filename', 'asc')->get();
        //$mode = $request->input('mode');
        if (\Auth::user()->isAdmin) {
            return view('forum.forumAdmin', compact('discussions', 'oldValue', 'count'));
        } else {
            return view('forum.forum', compact('discussions', 'oldValue', 'count'));
        }
    }
    //To Here 
    /**
    *create forum discussion
    *
    *@param Request $request
    *
    * @return void
    */
    public function createDiscussion(Request $request) {
    	$user_id = \Auth::user()->id;
    	$category_id = $request->get('category');
    	$title= $request->get('title');
    	$description = $request->get('description');

    	$mytime = Carbon::now() ->timezone(\Config::get('app.timezone'));

    	DB::table('forumdiscussion') ->insert(
                ['user_id' => $user_id, 'category_id' => $category_id, 'title' => $title, 'description' => $description, 'created_at' => $mytime->toDateTimeString()]
        );

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
            return redirect()->route('forumAdmin');

        } else{
            return redirect()->route('forum');
        }
    }
    /**
    *allow users to like to unlike discussion threads.
    *
    *@param Request $request
    *
    * @return void
    */
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

                    $user->notify(new LikedDiscussion($userWhoLiked));
                    
                    DB::table('discussionUserLike') ->insert(
                            ['discussion_id' => $discussionId,'user_id' => $userId]
                    );
                    $numberOfLikes = DB::table('forumdiscussion')
                        ->where('id', '=', $discussionId)
                        ->value('likes');
                    DB::table('forumdiscussion')
                        ->update(['likes' => $numberOfLikes+1]);
                } else {
                    DB::table('discussionUserLike')
                    -> where ('discussion_id', '=', $discussionId)
                    -> where ('user_id', '=', $userId)
                    -> delete();
                    
                    $numberOfLikes = DB::table('forumdiscussion')
                        ->where('id', '=', $discussionId)
                        ->value('likes');
                    DB::table('forumdiscussion')
                        ->update(['likes' => $numberOfLikes-1]);
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
    /**
    *show all the response for a discussion thread
    *
    *@param Request $request
    *
    * @return String $discussionId
    */
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
    /**
    *create response for a discussion thread
    *
    *@param Request $request
    *
    * @return string $discussionId
    */
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
        
        $timeCreated = $mytime->toDateTimeString();
        //\Notification::send($userWhoCreatedDisc, new DiscussionResponsed($forumpageUrl));
        
    	DB::table('forumresponse')->insert(
                ['user_id' => $user_id, 'discussion_id' => $discussion_id, 'content' => $content, 'created_at' => $timeCreated]
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
                    $thisComment = DB::table('forumresponse')
                            -> where ('user_id', '=', $user_id)
                            -> where ('created_at', '=', $timeCreated)                            
                            -> first();
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
                            -> where ('comment_id', '=', $thisComment->id)
                            -> get();
                    if($tagExistInFTD == null){
                        DB::table('forumtags_discussion') -> insert(
                            ['forum_tag' => $tagname, 'discussion_id'=>$discussion_id, 'comment_id'=> $thisComment->id]
                        );
                    }
                }
            }
        }

        //return redirect()->route('forumpage')->with('discussionId',$discussion_id);
        return \View::make('forum.forumpage')->with('discussionId',$discussion_id);
    }
    
    //Added this
    /**
    *receive all discussion thread related to the chosen tag, and return them
    *
    *@param Request $request
    *
    * @return String $forumTag
    */
    public function showTagPosts(Request $request){
        $forumTag = $request->get('id');

        return view('forum.forumShowTagPosts', compact('forumTag'));

    }
    
   /**
    *allow user to delete a discussion thread
    *
    *@param Request $request
    *
    * @return void
    */
    public function deleteDiscussion(Request $request){
        $discussion_id = $request->get('discussionId');

        $tagsInThisDiscussion = DB::table('forumtags_discussion')
                    -> where ('discussion_id', '=', $discussion_id)
                    -> get();

        foreach($tagsInThisDiscussion as $thisTag) {
            $tagCountInForumTags = DB::table('forumtags')
                    -> where ('forum_tag', '=', $thisTag->forum_tag)
                    -> value('count');
            //exit($tagCountInForumTags);
            if ($tagCountInForumTags == 1) {
                DB::table('forumtags') 
                -> where ('forum_tag', '=', $thisTag->forum_tag)
                -> delete();
            } else {
                DB::table('forumtags')
                -> where ('forum_tag', '=', $thisTag->forum_tag)
                -> update(['count' => $tagCountInForumTags - 1]);
            }
            
        }
        DB::table('forumtags_discussion')
        -> where ('discussion_id', '=', $discussion_id)
        -> delete();

        DB::table('forumresponse')
        -> where ('discussion_id', '=', $discussion_id)
        -> delete();

        DB::table('forumdiscussion')->where('id','=', $discussion_id)->delete();
        
        return redirect('forumAdmin');
    }
    /**
    *allow admin user to delete a discussion comment
    *
    *@param Request $request
    *
    * @return void
    */
    public function deleteComment(Request $request){
        $comment_id = $request->get('commentID');
        $discussion_id = $request->get('discussionID');

        $tagsInThisComment = DB::table('forumtags_discussion')
                    -> where ('discussion_id', '=', $discussion_id)
                    -> where ('comment_id', '=', $comment_id)
                    -> get();

        if ($tagsInThisComment != null) {
            foreach($tagsInThisComment as $thisTag) {
                $tagCountInForumTags = DB::table('forumtags')
                        -> where ('forum_tag', '=', $thisTag->forum_tag)
                        -> value('count');
                //exit($tagCountInForumTags);
                if ($tagCountInForumTags == 1) {
                    DB::table('forumtags') 
                    -> where ('forum_tag', '=', $thisTag->forum_tag)
                    -> delete();
                } else {
                    DB::table('forumtags')
                    -> where ('forum_tag', '=', $thisTag->forum_tag)
                    -> update(['count' => $tagCountInForumTags - 1]);
                }
                
            }
            DB::table('forumtags_discussion')
            -> where ('discussion_id', '=', $discussion_id)
            -> where ('comment_id', '=', $comment_id)
            -> delete();
        }
        
        DB::table('forumresponse')
        -> where ('id', '=', $comment_id)
        -> delete();
        
        return redirect('/forumResponsePage?id='.$discussion_id);
    }
    /**
    *allow admin user to close a discussion thread
    *
    *@param Request $request
    *
    * @return void
    */
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
        return redirect('forumAdmin');
    }
    /**
    *allow admin user to addCategory
    *
    *@param Request $request
    *
    * @return void
    */
	public function addCategory(Request $request){
	    $error="";
	    try{
	        $categoryName = $request->get('categoryName');
                $allCategory = DB::table('category') -> get();
                    $noOfCategories = sizeof($allCategory);
                     $categoryID = $noOfCategories + 1;

            foreach($allCategory as $category){
                $categoryNamefromDB = $category->categoryname;
                if($categoryName == $categoryNamefromDB ){
                   $error = "failed";

                }
            }
            if ($error == ""){
                 DB::table('category')->insert(['id' => $categoryID, 'categoryname' => $categoryName]);

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
    /**
    *allow user to filter the tags
    *
    *@param Request $request
    *
    * @return array $tagsForSearch
    */
    public function filterTags(Request $request) {
        $filterInput = $request->input('studentInput');
        $tagsForSearch = null;


        $tagsForSearch = Forumtag::where('forum_tag','like','%'.$filterInput.'%')
                ->get();

        return view('forum.forumAdmin', compact('tagsForSearch'));
    }
}
