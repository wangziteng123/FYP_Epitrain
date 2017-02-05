<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fileentry;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Imagick;

class FileEntryController extends Controller
{
    public function index()
	{
		$entries = Fileentry::all();
 
		return view('fileentries.index', compact('entries'));
	}
 
	public function add(Request $request) {
 
		//get category, price and description
		$category = $request->input('category');
		$price = $request->input('price');
		$description = $request->input('description');

 		//create record in fileentry table
		//$file = Request::file('filefield');
		$file = $request->file('filefield');
		$extension = $file->getClientOriginalExtension();
		Storage::disk('s3')->put('ebooks/'.$file->getFilename().'.'.$extension,  File::get($file));
		$entry = new Fileentry();
		$entry->mime = $file->getClientMimeType();
		$entry->original_filename = $file->getClientOriginalName();
		$entry->filename = $file->getFilename().'.'.$extension;

		$entry->category = $category;
		$entry->price = $price;
		$entry->description = $description;
 
		$entry->save();
 
		return redirect('fileentry');
		
	}

	public function get($filename){
		//$entry = Fileentry::where('filename', '=', $filename)->firstOrFail();
		//$file = Storage::disk('s3')->get($filename);
		// $url = "s3".env('S3_REGION')."amazonaws.com/".env('S3_BUCKET')."/ebooks/".$filename;
 	// 	echo $url;
		/*return (new Response($file, 200))
              ->header('Content-Type', $file->getClientMimeType());*/
        //return redirect($url);

        
        //version 3.6
        $entry = Fileentry::where('filename', '=', $filename)->firstOrFail();
        $url = "s3-".env('S3_REGION')."amazonaws.com/".env('S3_BUCKET')."/ebooks/".$entry->filename;
		//$file = Storage::disk('s3')->get("http://sample-env-1.2uqmcfeudi.us-west-2.elasticbeanstalk.com/ebook/".$entry->filename.".pdf");
 		$file = Storage::disk('s3')->get('/ebooks/'.$entry->filename);
		return (new Response($file, 200))
               ->header('Content-Type', $entry->mime);

	}

	public function getPdfViewer($filename) {
		$add = "fileentry/get/".$filename; 
		$baseUrl = url($add);
		//$pdfUrl = "http://localhost:8000/fileentry/get/php8D98.tmp.pdf";
		return redirect()->route('pdfreader', array('file' => $baseUrl));
	}


	public function getPreview($filename){
		// $im = new \Imagick( "" );
		// $entry = Fileentry::where('filename', '=', $filename)->firstOrFail();
		// $file = Storage::disk('local')->get($entry->filename);
 
		// return (new Response($file, 200))
  //             ->header('Content-Type', $entry->mime);
	}

	public function delete($filename){
		//$im = new \Imagick( "" );
		$entry = Fileentry::where('filename', '=', $filename)->firstOrFail();

		if($entry) {
			//$file = Storage::disk('s3')->delete($entry->filename);
			//version 3.6
			$entry->delete();
			$url = "s3-".env('S3_REGION')."amazonaws.com/".env('S3_BUCKET')."/ebooks/".$entry->filename;
			echo $url;
			Storage::disk('s3')->delete('/ebooks/'.$entry->filename);
	 		return redirect('fileentry');
			// return (new Response("Delete Successfully", 200))
	  //             ->header('Content-Type', "text/html");
	    } else {
	          	return redirect('fileentry');
	          	// return (new Response("No such file", 200))
	           //    ->header('Content-Type', "text/html");
	    } 
		
	}
}
