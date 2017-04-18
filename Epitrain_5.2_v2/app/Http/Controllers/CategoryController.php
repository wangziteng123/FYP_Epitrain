<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Fileentry;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * CategoryController Class used for Customize Categories function
 */
class CategoryController extends Controller
{
    /**
	 * index function generate a new category with name limited to 20 digits
	 *
	 * @param Request $request which is the request to add a new category with a name
	 * @return view of the function page
	 */
    public function index(Request $request) {
    	$category = $request->input('categoryName');

    	if(strcmp($category, 'All') == 0) {
    		$entries = Fileentry::paginate(20);
    		return view('category.category', compact('entries'),compact('category'));
    	} 
    	
    	$entries = Fileentry::where('category', $category)
    	->paginate(20);
    	
    	return view('category.category', compact('entries'),compact('category'));
    }
    
	/**
	 * indexEdit function change the name of a category with the length of the function limited to 20 digits
	 *
	 * @param Request $request which is the request to modify the name of a category
	 * @return view of success or error message
	 */
    public function indexEdit(Request $request) {
    	$category = $request->input('categoryName');

    	if(strcmp($category, 'All') == 0) {
    		$entries = Fileentry::paginate(20);
    		return view('category.category', compact('entries'),compact('category'));
    	} 
    	
    	$entries = Fileentry::where('category', $category)
    	->paginate(20);
    	
    	return redirect()->back();
    }
	
	/**
	 * setForumCategory function change the categories activated for forum
	 *
	 * @param Request $request which takes in an array of all the cateogries admin selected for the forum
	 * @return view of success or error message
	 */
    public function setForumCategory(Request $request) {
        $input = $request->toArray();
        $enabledCat = array();
        list($catList, $status) = array_divide($input);
        foreach ($catList as $forumCat) {
            $catArr = explode("_", $forumCat);
            $categoryName = "";
            foreach ($catArr as $component) {
                $categoryName .= $component." ";
            }
            $categoryName = substr($categoryName, 0, -2);
            $enabledCat[] = $categoryName;
        }
        $categoriesFromDB = DB::table('category')->get();
        foreach ($categoriesFromDB as $catFromDB) {
            $catFromDBName = $catFromDB->categoryname;
            //exit(var_dump($enabledCat));
            if (in_array($catFromDBName, $enabledCat) == null) {
                DB::table('category')
                    ->where('categoryname', $catFromDBName)
                    ->update(['shownInForumCat' => 0]);
            } else {
                DB::table('category')
                    ->where('categoryname', $catFromDBName)
                    ->update(['shownInForumCat' => 1]);
            }
        }
        return view('category.category');
    }

	/**
	 * setEbookCategory function change the categories activated for ebooks
	 *
	 * @param Request $request which takes in an array of all the cateogries admin selected for the forum
	 * @return view of success or error message
	 */
    public function setEbookCategory(Request $request) {
        $input = $request->toArray();
        $enabledCat = array();
        list($catList, $status) = array_divide($input);
        foreach ($catList as $ebookCat) {
            $catArr = explode("_", $ebookCat);
            $categoryName = "";
            foreach ($catArr as $component) {
                $categoryName .= $component." ";
            }
            $categoryName = substr($categoryName, 0, -2);
            $enabledCat[] = $categoryName;
        }
        $categoriesFromDB = DB::table('category')->get();
        foreach ($categoriesFromDB as $catFromDB) {
            $catFromDBName = $catFromDB->categoryname;
            //exit(var_dump($enabledCat));
            if (in_array($catFromDBName, $enabledCat) == null) {
                DB::table('category')
                    ->where('categoryname', $catFromDBName)
                    ->update(['shownInEbookCat' => 0]);
            } else {
                DB::table('category')
                    ->where('categoryname', $catFromDBName)
                    ->update(['shownInEbookCat' => 1]);
            }
        }
        return view('category.category');
    }


	/**
	 * setEbookShortcut function change the categories in user account ebook category dropdown list
	 *
	 * @param Request $request which takes in an array of all the cateogries admin selected for the category dropdown list
	 * @return view of new categories
	 */
    public function setEbookShortcut(Request $request) {
        $input = $request->toArray();
        $enabledCat = array();
        list($catList, $status) = array_divide($input);
        foreach ($catList as $userCat) {
            $catArr = explode("_", $userCat);
            $categoryName = "";
            foreach ($catArr as $component) {
                $categoryName .= $component." ";
            }
            $categoryName = substr($categoryName, 0, -2);
            $enabledCat[] = $categoryName;
        }
        $categoriesFromDB = DB::table('category')->get();
        foreach ($categoriesFromDB as $catFromDB) {
            $catFromDBName = $catFromDB->categoryname;
            //exit(var_dump($enabledCat));
            if (in_array($catFromDBName, $enabledCat) == null) {
                DB::table('category')
                    ->where('categoryname', $catFromDBName)
                    ->update(['shownInUserCategories' => 0]);
            } else {
                DB::table('category')
                    ->where('categoryname', $catFromDBName)
                    ->update(['shownInUserCategories' => 1]);
            }
        }
        return view('category.category');
    }

    /**
	 * addCategory function add in a new category
	 *
	 * @param Request $request which takes in a new category name
	 * @return view of success or error message
	 */
    public function addCategory(Request $request){
        $status="";
        $categoryName = $request->get('categoryName');
        $allCategory = DB::table('category') -> get();
        $noOfCategories = sizeof($allCategory);
        $categoryID = $noOfCategories + 1;
        //exit(var_dump($categoryName));
        if (strcmp($categoryName,"") == 0) {

            $status = "empty";
        }
        if ($status == ""){
            foreach($allCategory as $category){
                $categoryNamefromDB = $category->categoryname;
                if($categoryName == $categoryNamefromDB ){
                   $status = "failed";
                } 
            }
        }
        if ($status == ""){
             DB::table('category')->insert(['categoryname' => $categoryName]);

             $status = "Category successfully added";
        }

        if($status == "Category successfully added") {
            return redirect('category')->with('success','Category successfully added!');
        } else if($status == "empty") {
            return redirect('category')->with('empty','Please enter category!');
        } else {
            return redirect('category')->with('failure','Category already exist!');   
        }
    }
    
	/**
	 * editCategory function edit the name of an existing category
	 *
	 * @param Request $request which takes in a new name for an existing category and the exiting cateogry name
	 * @return view of success or error message
	 */
    public function editCategory(Request $request){
        $status="";
        $newCategoryName = $request->get('categoryName');
        
        $categoryNameId = $request->get('category');
        $oldCategory = DB::table('category')
            -> where ('id', '=', $categoryNameId)
            -> value('categoryname');
        
        if (strcmp($newCategoryName,"") == 0) {

            $status = "empty";
        }
        if ($status == "" && $newCategoryName == $oldCategory){
            $status = "failed";
            /* foreach($allCategory as $category){
                $categoryNamefromDB = $category->categoryname;
                if($categoryName == $categoryNamefromDB ){
                   $status = "failed";
                } 
            } */
        }
        if ($status == ""){
             DB::table('category')
                ->where('id', '=', $categoryNameId)
                ->update(['categoryname' => $newCategoryName]);

             $status = "Category successfully edited";
        }

        if($status == "Category successfully edited") {
            return redirect('categoryEdit')->with('success','Category successfully edited!');
        } else if($status == "empty") {
            return redirect('categoryEdit')->with('empty','Please enter category!');
        } else {
            return redirect('categoryEdit')->with('failure','Category already exist!');   
        }
    }
		
}
