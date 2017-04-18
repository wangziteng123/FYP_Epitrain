<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * AdminController Class used for FAQ function
 */
class FaqController extends Controller
{
    
	/**
	 * index function generate the FAQ page
	 *
	 * @return view of the FAQ page
	 */
     public function index()
    {
        return \View::make('faq.faq');
    }

	/**
	 * create function create a new faq with question and answer
	 *
	 * @param Request $request takes in the question and answer content entered
	 * @return void
	 */
    public function create(Request $request)
    {
        $question = $request->input('question');
        $answer= $request->input('answer');
        
        DB::table('faq') ->insert(
            ['question' => $question, 'answer' => $answer]
        );
        
        return redirect()->route('faq');
    }

	/**
	 * delete function delete a faq with question and answer
	 *
	 * @param Request $request takes in the question and answer of an existing FAQ
	 * @return void
	 */
    public function delete(Request $request){
        $id = $request->get('id');
        
        DB::table('faq')
            ->where('id', '=', $id)
            ->delete();
        return redirect()->route('faq');  
    }

	/**
	 * edit function edit the content of a faq
	 *
	 * @param Request $request takes in the question and answer content entered
	 * @return void
	 */
    public function edit(Request $request) {
        $id = $request->get('id');
        $question = $request->input('question');
        $answer= $request->input('answer');

        DB::table('faq')
            ->where('id', '=', $id)
            ->update(['question' => $question,'answer' => $answer]);

        return redirect()->route('faq');  
    }
}
