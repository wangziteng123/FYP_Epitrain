<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fileentry;
use App\Forumtag;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * SearchController Class used for search function
 */
class SearchController extends Controller
{
	/**
	* index function generate a view of the search function
	*
	* @return view of the page
	*/
	public function index() {
		return view('search.index');
	}

	/**
	* find function search takes in the parameter as search criteria and search for materials or users or classes
	*
	* @return Json of the materials or users or classes by search criteria
	*/
    public function find(Request $request) {
    	return Fileentry::search($request->get('q'))->get()->toJson();
    }

}
