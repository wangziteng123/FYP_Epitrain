<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Fileentry;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    //
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
