<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');

Route::get('user/activation/{token}', 'Auth\AuthController@activateUser')->name('user.activate');

//Route::get('/update', ['middleware' => 'auth', 'uses=>UserController@index', function() {
	//return view('usermanage.updateInfo');
//}]);

//Route::resource('users', 'UserController');

Route::group(['middleware' => ['auth','admin']], function() {
	
	Route::post('/store', 'UserController@store');
	Route::get('um/tocreate', 'HomeController@create');
	Route::delete('fileentry/delete/{filename}', [
	'as'=>'deleteentry', 'uses'=>'FileEntryController@delete']);
});


Route::group(['middleware' => 'auth'], function() {
	Route::get('/update', 'UserController@index');
	Route::get('users/{id}', 'UserController@update');
	//For file upload and download
	Route::get('fileentry', 'FileEntryController@index');

	Route::get('fileentry/get/{filename}', [
		'as'=>'getentry', 'uses'=>'FileEntryController@get']);

	Route::get('fileentry/getViewer/{filename}', [
		'as'=>'getviewer', 'uses'=>'FileEntryController@getPdfViewer']);

	Route::post('fileentry/add', [
		'as'=>'addentry', 'uses'=>'FileEntryController@add']);

	Route::get('mylibrary', 'MyLibraryController@index');
	Route::get('pdfreader', [
		'as'=>'pdfreader', 'uses'=>'MyLibraryController@getViewer']);

});

// // Download Route
// Route::get('download/{filename}', function($filename)
// {
//     // Check if file exists in app/storage/file folder
//     $file_path = storage_path() .'/file/'. $filename;
//     if (file_exists($file_path))
//     {
//         // Send Download
//         return Response::download($file_path, $filename, [
//             'Content-Length: '. filesize($file_path)
//         ]);
//     }
//     else
//     {
//         // Error
//         exit('Requested file does not exist on our server!');
//     }
// })
// ->where('filename', '[A-Za-z0-9\-\_\.]+');


