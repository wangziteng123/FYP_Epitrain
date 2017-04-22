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
use DB;

/**
 * FileEntryController Class used for ebooks such as adding and deleting
 */
class FileEntryController extends Controller
{
	/**
	* redirect user to file entry view
	*
	* @return void
	*/
    public function index()
	{
		$entries = Fileentry::orderBy('original_filename', 'asc')->paginate(12);
 		$mode = "original_filename-asc";

		return view('fileentries.index', compact('entries','mode'));
	}
	/**
	*sort the books in ascending and descending order depending on user's clicks
	*
	* @para Request $request takes in the sorting field such as name, category price
	*
	* @return void
	*/
	public function sort(Request $request)
	{
		$sortField = $request->input('sortField');
		$mode = $request->input('mode');
		$entries = Fileentry::orderBy('original_filename', 'asc')->paginate(12);
		if ($mode == null) {
			$mode = "original_filename-asc";
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
	/**
	*filter the books according to specific category
	*
	* @para Request $request takes in filter category
	*
	* @return void
	*/
 	public function filter(Request $request)
	{
		$mode = $request->input('mode');
		if ($mode == null) {
			$mode = "original_filename-asc";
		}
		$modeArr = explode("-", $mode);
		$filterCat = $request->input('filterCat');
		if (strlen($filterCat) != 0 && strcmp($filterCat,"all") != 0) {
			$entries = Fileentry::orderBy($modeArr[0], $modeArr[1])
			->where('category', '=', $filterCat)->paginate(12);
		} else {
			$entries = Fileentry::orderBy($modeArr[0], $modeArr[1])->paginate(12);
		}
	
		return view('fileentries.index', compact('entries','mode'));
	}
	/**
	*Add book, and set the price, description, category for the book. Add preview for the book
	*
	* @para Request $request takes in category, price, description, and sample file. Sample file is only a short preview of the book.
	*
	* @return string $success- let user know if the book is successfully added
	*/
	public function add(Request $request) {
 		
 		$this->validate($request, [
		    'price' => 'required|numeric|min:0.51',
		    'category' => 'required',
		    'description' => 'required',
		    //'filefield' => 'required',
		]);

		//get category, price and description
		$category = $request->input('category');
		$price = $request->input('price');
		$description = $request->input('description');
		$file = $request->file('filefield');
		$sample = $request->file('samplefile');

		if ($file != null) {
			$n_file = File::get($file);
			$extension = $file->getClientOriginalExtension();
			if($extension === 'xlsm' || $extension === 'xls' || $extension === 'xlsx'){
		      	$encrypted = Crypt::encrypt($n_file);
		      	Storage::disk('s3')->put('spreadsheets/'.$file->getFilename().'.'.$extension,  $encrypted);
		    } else {
					//uncomment to try on local environment
		    	//Storage::disk('local')->put($file->getFilename().'.'.$extension,  $n_file); //taken out 'ebooks'. for testing
                Storage::disk('s3')->put('/ebooks/'.$file->getFilename().'.'.$extension,  $n_file);
		  }
			if($sample != null){
				
				$sample_content = File::get($sample);
				//uncomment to try on local environment
				//Storage::disk('local')->put($sample->getFilename().'.'.$sample->getClientOriginalExtension(), $sample_content);
				Storage::disk('s3')->put('/ebooks/'.$sample->getFilename().'.'.$sample->getClientOriginalExtension(), $sample_content);
			}
			
			$entry = new Fileentry();
			$entry->mime = $file->getClientMimeType();
			$entry->original_filename = $file->getClientOriginalName();
			$entry->filename = $file->getFilename().'.'.$extension;

			$entry->category = $category;
			$entry->price = $price;
			$entry->description = $description;
			
			if($sample != null){
                DB::table('fileentries_sample')
                    -> insert(['filename' => $file->getFilename(), 'sample_filename' => $sample->getFilename()]);
				$sampleID = DB::table('fileentries_sample')
                    -> where('sample_filename', '=', $sample->getFilename())
                    -> orderby('sample_id','DESC')
                    -> first();
                
                $entry->sample_id= $sampleID->sample_id;
			}
	 
			$entry->save();
	 
			return redirect('fileentry')->with('success', "File successfully uploaded!");
		} else {
			return redirect('fileentry')->with('failure', "You haven't chosen any file to upload.");
		}	
	}
	/**
	*edit book for its price, description, category for the book
	*
	* @para Request $request takes in category, price, description.
	*
	* @return String $success- let user know if the book is successfully added
	*/
	public function edit(Request $request) {
 		
 		$this->validate($request, [
		    'price' => 'required|min:0.51',
		    'category' => 'required',
		    'description' => 'required',
		]);

		//get category, price and description
		$category = $request->input('category');
		$price = $request->input('price');
		$description = $request->input('description');
		$oldFileName = $request->input('oldFileName');
		$sample = $request->file('samplefile');
		
		$entry = Fileentry::where('filename', '=', $oldFileName)->first();
		//exit(var_dump($sample));
			if($sample != null && $entry != null){

				if($entry->sample_id != null){
					// delete the old sample first
					//Storage::disk('local')->delete($entry->samplename.'.'.$sample->getClientOriginalExtension());
					$sampleEntry = DB::table('fileentries_sample')
                    -> where('sample_id', '=', $entry->sample_id)
                    -> first();
                    //uncomment to try on local environment
                    
                    //Storage::disk('local')->delete($sampleEntry->sample_filename.".pdf");
                    //Storage::disk('s3')->delete('/ebooks/'.$entry->samplename.'.'.$sample->getClientOriginalExtension());
                    if($sampleEntry != null) {
                    	 Storage::disk('s3')->delete('/ebooks/'.$sampleEntry->sample_filename.'.'."pdf");
                    
	                    DB::table('fileentries_sample')
	                    -> where('sample_id', '=', $entry->sample_id)
	                    -> delete();
                    }
				}
				$newSamplename = $sample->getFilename();
				$entry->samplename = $newSamplename;
				$samplecontent = File::get($sample);
                
				//Storage::disk('local')->put($sample->getFilename().'.'.$sample->getClientOriginalExtension(), $samplecontent);
				//uncomment to try on local environment
				Storage::disk('s3')->put('/ebooks/'.$sample->getFilename().'.'.$sample->getClientOriginalExtension(), $samplecontent);
				
                DB::table('fileentries_sample')
                    -> insert(['filename' => $oldFileName, 'sample_filename' => $sample->getFilename()]);
				$sampleID = DB::table('fileentries_sample')
                    -> where('sample_filename', '=', $sample->getFilename())
                    -> orderby('sample_id','DESC')
                    -> first();
                DB::table('fileentries')
                ->where('filename', $oldFileName)
                ->update(['category' => $category,
											'price' => $price,
											'description' => $description,
											'sample_id' => $sampleID->sample_id]);
			} else{
				DB::table('fileentries')
							->where('filename', $oldFileName)
							->update(['category' => $category,
												'price' => $price,
												'description' => $description]);
			
			}
		
		return redirect('fileentry')->with('success', "File updated successfully!");
	}
	/**
	*get book for reading
	*
	* @para string $filename .
	*
	* @return String $success- let user know if the book is successfully added
	*/
	public function get($filename){
        
        //version 3.6
        $entry = Fileentry::where('filename', '=', $filename)->firstOrFail();
        //dd($entry);
        $url = "s3-".env('S3_REGION')."amazonaws.com/".env('S3_BUCKET')."/ebooks/".$entry->filename;
		//$file = Storage::disk('s3')->get("http://sample-env-1.2uqmcfeudi.us-west-2.elasticbeanstalk.com/ebook/".$entry->filename.".pdf");
 		//$entryName = substr($entry->filename, 0, -4);
        
        $file = Storage::disk('s3')->get('/ebooks/'.$entry->filename);
		//uncomment to try on local environment
		//$file = Storage::disk('local')->get($entry->filename);
		return (new Response($file, 200))
               ->header('Content-Type', $entry->mime);

	}


     /**
            * Deprecated
            * Generate a HTTP response to allow users to download ebooks.
            * No longer used due to change in requirement.
            *
            *@param $filename
            *       name of the file in the database
            *
            *@return HTTPResponse with the specified file if found
            *        HTTPRedirect to previous page if the specified file is not found
            */
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
	/**
	*open the book for reading into pdf viewer
	*
	* @para string $filename takes in the name of the book being read.
	*
	* @return array $file
	*/
	public function getPdfViewer($filename) {
		$add = "fileentry/get/".$filename;
		//$baseUrl = "http://localhost:8000/".$add;
		$baseUrl = url($add);
		//$pdfUrl = "http://localhost:8000/fileentry/get/php8D98.tmp.pdf";
		return redirect()->route('pdfreader', array('file' => $baseUrl));
	}
	/**
	*get the sample to see
	*
	* @para int $sampleID takes in the name of the book being read.
	*
	* @return pdf file
	*/
    public function getSample($sampleID){
		//$entry = Fileentry::where('filename', '=', $filename)->firstOrFail();
		//$file = Storage::disk('s3')->get($filename);
		// $url = "s3".env('S3_REGION')."amazonaws.com/".env('S3_BUCKET')."/ebooks/".$filename;
		/*return (new Response($file, 200))
              ->header('Content-Type', $file->getClientMimeType());*/
        //return redirect($url);

        
        //version 3.6
        $entry = DB::table('fileentries_sample')
            -> where('sample_id', '=', $sampleID)
            -> first();
        //dd($entry);
        //dd($entry);
        //$entry = Fileentry::where('filename', '=', $filename)->firstOrFail();
        //$url = "s3-".env('S3_REGION')."amazonaws.com/".env('S3_BUCKET')."/ebooks/".$entry->filename;
		//$file = Storage::disk('s3')->get("http://sample-env-1.2uqmcfeudi.us-west-2.elasticbeanstalk.com/ebook/".$entry->filename.".pdf");
 		
        $file = Storage::disk('s3')->get('/ebooks/'.$entry->sample_filename.".pdf");
		
        //uncomment to try on local environment
		//$file = Storage::disk('local')->get('/ebooks/'.$entry->filename);
        
        //$file = Storage::disk('local')->get($entry->sample_filename.".pdf");
        return (new Response($file, 200))
               ->header('Content-Type', "application/pdf");

	}

	/**
	*delete the book
	*
	* @para string $filename
	*
	* @return void
	*/
	public function delete($filename){
		//$im = new \Imagick( "" );
		$entry = Fileentry::where('filename', '=', $filename)->firstOrFail();

		if($entry) {
			//$file = Storage::disk('s3')->delete($entry->filename);
			//version 3.6
			$entry->delete();
			$url = "s3-".env('S3_REGION')."amazonaws.com/".env('S3_BUCKET')."/ebooks/".$entry->filename;
			echo $url;
            
            $sampleEntry = DB::table('fileentries_sample')
            -> where('sample_id', '=', $entry->sample_id)
            -> first();
            
            //uncomment for localhost
            
            //Storage::disk('local')->delete($sampleEntry->sample_filename.".pdf");
			//Storage::disk('local')->delete('/ebooks/'.$entry->filename);
            if($sampleEntry != null){
                Storage::disk('s3')->delete('/ebooks/'.$sampleEntry->sample_filename.".pdf");
                DB::table('fileentries_sample')
                -> where('sample_id', '=', $entry->sample_id)
                -> delete();
            }
            
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
