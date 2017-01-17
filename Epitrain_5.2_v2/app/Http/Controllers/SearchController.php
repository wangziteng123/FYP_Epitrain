<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fileentry;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
	public function index() {
		return view('search.index');
	}

    public function find(Request $request) {
    	return Fileentry::search($request->get('q'))->get()->toJson();
    }

}
