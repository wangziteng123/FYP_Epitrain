<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fileentry;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\Filesystem;
use Imagick;
use Crypt;

class FileEntryController extends Controller
{
    public function index()
	{
		$entries = Fileentry::orderBy('original_filename', 'asc')->paginate(12);
 		$mode = "name-asc";

		return view('fileentries.index', compact('entries','mode'));
	}
	public function sort(Request $request)
	{
		$sortField = $request->input('sortField');
		$mode = $request->input('mode');
		$entries = Fileentry::orderBy('original_filename', 'asc')->paginate(12);
		if ($mode == null) {
			$mode = "name-asc";
		}
		$modeArr = explode("-", $mode);
		if ($sortField == $modeArr[0] && $modeArr[1] == "asc") {
			//exit($sortField);
			$entries = Fileentry::orderBy($sortField, 'desc')->paginate(12);
			$mode = $sortField."-desc";
		} else if ($sortField == $modeArr[0] && $modeArr[1] == "desc") {
			$entries = Fileentry::orderBy($sortField, 'asc')->paginate(12);
			$mode = $sortField."-asc";
		} else {
			$entries = Fileentry::orderBy($sortField, 'asc')->paginate(12);
			$mode = $sortField."-asc";
		}
 
		return view('fileentries.index', compact('entries','mode'));
	}
 	public function filter(Request $request)
	{
		$mode = $request->input('mode');
		if ($mode == null) {
			$mode = "name-asc";
		}
		$modeArr = explode("-", $mode);
		$filterCat = $request->input('filterCat');
		if (strlen($filterCat) != 0) {
			$entries = Fileentry::orderBy($modeArr[0], $modeArr[1])
			->where('category', '=', $filterCat)->get();
		} else {
			$entries = Fileentry::orderBy($modeArr[0], $modeArr[1])->paginate(12);
		}
		
		return view('fileentries.index', compact('entries','mode'));
	}
	public function add(Request $request) {
 
		//get category, price and description
		$category = $request->input('category');
		$price = $request->input('price');
		$description = $request->input('description');
		$file = $request->file('filefield');

		if ($file != null) {
			$n_file = File::get($file);
			$extension = $file->getClientOriginalExtension();
			if($extension === 'xlsm' || $extension === 'xls' || $extension === 'xlsx'){
		      	$encrypted = Crypt::encrypt($n_file);
		      	Storage::disk('s3')->put('spreadsheets/'.$file->getFilename().'.'.$extension,  $encrypted);
		    } else {
		    	Storage::disk('s3')->put('ebooks/'.$file->getFilename().'.'.$extension,  $n_file);
		    }	
			$entry = new Fileentry();
			$entry->mime = $file->getClientMimeType();
			$entry->original_filename = $file->getClientOriginalName();
			$entry->filename = $file->getFilename().'.'.$extension;

			$entry->category = $category;
			$entry->price = $price;
			$entry->description = $description;
	 
			$entry->save();
	 
			return redirect('fileentry')->with('success', "File successfully uploaded!");
		} else {
			return redirect('fileentry')->with('failure', "You haven't chosen any file to upload.");
		}	
	}

	public function edit(Request $request) {
 
		//get category, price and description
		$category = $request->input('category');
		$price = $request->input('price');
		$description = $request->input('description');
		$file = $request->file('filefield');
		$oldFileName = $request->input('oldFileName');
		$entry = Fileentry::where('filename', '=', $oldFileName)->firstOrFail();

		if ($entry) {
			if($file != null) {
				//delete old file
				Storage::disk('s3')->delete('/ebooks/'.$entry->filename);
				//add new file
		 		$n_file = File::get($file);
				$extension = $file->getClientOriginalExtension();
				if($extension === 'xlsm' || $extension === 'xls' || $extension === 'xlsx'){
			      	$encrypted = Crypt::encrypt($n_file);
			      	Storage::disk('s3')->put('spreadsheets/'.$file->getFilename().'.'.$extension,  $encrypted);
			    } else {
			    	Storage::disk('s3')->put('ebooks/'.$file->getFilename().'.'.$extension,  $n_file);
			    }	
				$entry->mime = $file->getClientMimeType();
				$entry->original_filename = $file->getClientOriginalName();
				$entry->filename = $file->getFilename().'.'.$extension;

				$entry->category = $category;
				$entry->price = $price;
				$entry->description = $description;
		 
				$entry->save();
		    } else {
		    	$entry->category = $category;
				$entry->price = $price;
				$entry->description = $description;
		 
				$entry->save();
		    }
		} else {
			$n_file = File::get($file);
			$extension = $file->getClientOriginalExtension();
			if($extension === 'xlsm' || $extension === 'xls' || $extension === 'xlsx'){
		      	$encrypted = Crypt::encrypt($n_file);
		      	Storage::disk('s3')->put('spreadsheets/'.$file->getFilename().'.'.$extension,  $encrypted);
		    } else {
		    	Storage::disk('s3')->put('ebooks/'.$file->getFilename().'.'.$extension,  $n_file);
		    }	
			$entry = new Fileentry();
			$entry->mime = $file->getClientMimeType();
			$entry->original_filename = $file->getClientOriginalName();
			$entry->filename = $file->getFilename().'.'.$extension;

			$entry->category = $category;
			$entry->price = $price;
			$entry->description = $description;
	 
			$entry->save();			
		}	
		return redirect('fileentry')->with('success', "File updated successfully!");
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
	public function getDownload($filename)
	{
	    //PDF file is stored under project/public/download/info.pdf
	     /*$file_path = storage_path() .'\\app\\'. $filename;
	     echo $file_path;*/
	    $entry = Fileentry::where('filename', '=', $filename)->firstOrFail();
        $url = "s3-".env('S3_REGION')."amazonaws.com/".env('S3_BUCKET')."/spreadsheets/".$entry->filename;
		//$file = Storage::disk('s3')->get("http://sample-env-1.2uqmcfeudi.us-west-2.elasticbeanstalk.com/ebook/".$entry->filename.".pdf");
 		$file = Storage::disk('s3')->get('/spreadsheets/'.$entry->filename);
 		$mime_type = "";
 		
	    if ($file != null)
	     {      
	     	if (strpos($entry->filename,'xls') !== false) {
	     		$mime_type = "application/vnd.ms-excel";
	     	} else if (strpos($entry->filename,'xlsx') !== false) {
	     		$mime_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
	     	} else if (strpos($entry->filename,'xlsm') !== false) {
	     		$mime_type = "application/vnd.ms-excel.sheet.macroEnabled.12";
	     	}
	        return $this->respondDownload($file,$entry->filename,$mime_type);
	         //return response()->download($file, 'download.pdf', $headers);
	     }
	     else
	     {
	         // Error
	         exit('Requested file does not exist on our server!');
	     }
	  }
	/**
	 * Respond with a file download.
	 *
	 * @param $fileContent
	 * @param $fileName
	 * @param $mime
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function respondDownload($fileContent, $fileName, $mime)
	{
	    $response = response($fileContent, 200, [
            'Content-Type' => $mime,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);

        ob_end_clean(); 

        return $response;
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
