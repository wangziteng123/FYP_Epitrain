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
    return Redirect::action('Auth\AuthController@showLoginForm');
});

Route::auth();

Route::get('/home',  ['as'=>'home', 'uses'=>'HomeController@index']);

Route::get('user/activation/{token}', 'Auth\AuthController@activateUser')->name('user.activate');

Route::get('contact', ['as' => 'contact', 'uses' => 'AboutController@create']);

Route::post('contact', ['as' => 'contact_store', 'uses' => 'AboutController@store']);
//Route::get('/update', ['middleware' => 'auth', 'uses=>UserController@index', function() {
	//return view('usermanage.updateInfo');
//}]);

//Route::resource('users', 'UserController');

Route::group(['middleware' => ['auth','admin']], function() {
	
	Route::post('/store', 'UserController@store');
	Route::get('um/tocreate', 'HomeController@create');
	Route::get('/viewAllUsers', 'UserController@viewAllUsers');
	Route::delete('fileentry/delete/{filename}', [
	'as'=>'deleteentry', 'uses'=>'FileEntryController@delete']);
	
	Route::get('/forumAdmin', ['as'=>'forum', 'uses'=>'ForumController@indexAdmin']);
	Route::post('/deleteDiscussion', ['as' => 'deleteDiscussion', 'uses' => 'ForumController@deleteDiscussion']);
	Route::post('/closeDiscussion', ['as' => 'closeDiscussion', 'uses' => 'ForumController@closeDiscussion']);
});


Route::group(['middleware' => 'auth'], function() {
	Route::get('/', 'HomeController@index');
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
	Route::get('shop', 'HomeController@shop');
	Route::get('buy/{book_id}', ['as'=>'buy', 'uses'=>'LibraryController@buy']);
 
 
	Route::get('pdfreader', [
		'as'=>'pdfreader', 'uses'=>'MyLibraryController@getViewer']);

	Route::get('find', 'SearchController@find');
	Route::get('searchresult', 'SearchController@index');

	Route::get('shoppingcart',  [
		'as'=>'shoppingcart', 'uses'=>'ShoppingController@index']);
	Route::post('shoppingcart/add', [
		'as'=>'addShoppingcart', 'uses'=>'ShoppingController@add']);
	Route::post('shoppingcart/deleteShoppcart', 'ShoppingController@delete');

	Route::post('shoppingcart/addtolibrary', 'ShoppingController@addToLibrary');
	Route::post('shoppingcart/checkout', 'ShoppingController@checkout');
	
	
	Route::get('/forum', ['as'=>'forum', 'uses'=>'ForumController@index']);
	Route::get('/forumpage', ['as'=>'forumpage', 'uses'=>'ForumController@toPage']);
	Route::get('/forumResponsePage', ['as'=>'forumResponsePage', 'uses'=>'ForumController@showAllResponse']);
	Route::post('/createDiscussion', ['as' => 'createDiscussion', 'uses' => 'ForumController@createDiscussion']);
	Route::post('/createResponse', ['as' => 'createResponse', 'uses' => 'ForumController@createResponse']);
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


