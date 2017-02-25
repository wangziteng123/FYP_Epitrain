<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Fileentry;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    //
    public function index(Request $request) {
    	$category = $request->input('category');

    	if(strcmp($category, 'All') == 0) {
    		$entries = Fileentry::paginate(20);
    		return view('category.category', compact('entries'),compact('category'));
    	} 
    	
    	$entries = Fileentry::where('category', $category)
    	->paginate(20);
    	
    	return view('category.category', compact('entries'),compact('category'));
  }

		
}
