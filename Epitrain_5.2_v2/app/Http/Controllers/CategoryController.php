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
             DB::table('category')->insert(['id' => $categoryID, 'categoryname' => $categoryName]);

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
		
}
