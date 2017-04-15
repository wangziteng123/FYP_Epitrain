<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    //
     public function index()
    {
        //
        return \View::make('faq.faq');
    }

    public function create(Request $request)
    {
        $question = $request->input('question');
        $answer= $request->input('answer');
        
        DB::table('faq') ->insert(
            ['question' => $question, 'answer' => $answer]
        );
        
        return redirect()->route('faq');
    }

    public function delete(Request $request){
        $id = $request->get('id');
        
        DB::table('faq')
            ->where('id', '=', $id)
            ->delete();
        return redirect()->route('faq');  
    }

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
