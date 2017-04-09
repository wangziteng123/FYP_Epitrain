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
	
	//user routes
	Route::post('/store', 'UserController@store');
	Route::get('/createUser', 'HomeController@create');
	Route::get('/viewAllUsers', 'UserController@viewAllUsers');

	//file entries (ebook) routes
	Route::get('fileentry', 'FileEntryController@index');
	Route::delete('fileentry/delete/{filename}', [
	'as'=>'deleteentry', 'uses'=>'FileEntryController@delete']);
	Route::get('fileentry/sort', ['as' => 'fileSort', 'uses' => 'FileEntryController@sort']);
	Route::get('fileentry/filter', ['as' => 'fileFilter', 'uses' => 'FileEntryController@filter']);
	Route::post('fileentry/add', [
		'as'=>'addentry', 'uses'=>'FileEntryController@add']);
	Route::post('fileentry/edit', [
		'as'=>'editentry', 'uses'=>'FileEntryController@edit']);
	
	//forum routes
	Route::get('/forumAdmin', ['as'=>'forumAdmin', 'uses'=>'ForumController@indexAdmin']);
	Route::post('/deleteDiscussion', ['as' => 'deleteDiscussion', 'uses' => 'ForumController@deleteDiscussion']);
	Route::post('/closeDiscussion', ['as' => 'closeDiscussion', 'uses' => 'ForumController@closeDiscussion']);
    Route::post('/addCategory', ['as' => 'addCategory', 'uses' => 'CategoryController@addCategory']);
    Route::post('/editCategory', ['as' => 'editCategory', 'uses' => 'CategoryController@editCategory']);
    Route::post('/deleteComment', ['as' => 'deleteComment', 'uses' => 'ForumController@deleteComment']);
    Route::get('forumAdmin/sort', ['as' => 'discussionSort', 'uses' => 'ForumController@dsort']);
    
    //category route
	Route::get('/category', ['as' => 'category', 'uses' => 'CategoryController@index']);
	Route::get('/categoryEdit', ['as' => 'category', 'uses' => 'CategoryController@indexEdit']);
	Route::post('/setForumCat', ['as' => 'setForumCat', 'uses' => 'CategoryController@setForumCategory']);
	Route::post('/setEbookCat', ['as' => 'setEbookCat', 'uses' => 'CategoryController@setEbookCategory']);
	Route::post('/setEbookShortcut', ['as' => 'setEbookShortcut', 'uses' => 'CategoryController@setEbookShortcut']);


    //payment routes
	Route::post('/payment', ['as' => 'payment', 'uses' => 'PaymentController@index']);

	//FAQ routes
	Route::get('/faq', ['as' => 'faq', 'uses' => 'FaqController@index']);
	Route::get('/faq/create', function () {
	    return view('faq.create');
	});
	Route::post('/faq/createquestion', ['as' => 'faqcreate', 'uses' => 'FaqController@create']);
	Route::get('/faq/delete', ['as' => 'faqdelete', 'uses' => 'FaqController@delete']);
	Route::get('/faq/edit', function () {
	    return view('faq.edit');
	});
	Route::post('/faq/editFaq', ['as' => 'faqEdit', 'uses' => 'FaqController@edit']);

	//subscription routes
	Route::get('/subscriptionplan', function () {
	    return view('subscriptionplan.subscriptionplan');
	});
	Route::post('/addSubscriptionPlan', 'SubscriptionController@addSubscriptionPlan');
	Route::post('/deleteSubscriptionPlan', 'SubscriptionController@deleteSubscriptionPlan');

	//Class management routes
	Route::get('classmanagement', 'ClassManagementController@index');
	Route::post('classmanagement/addCourse', ['as' => 'addCourse', 'uses' => 'ClassManagementController@addCourse']);
	Route::post('classmanagement/deleteCourse', ['as' => 'deleteCourse', 'uses' => 'ClassManagementController@deleteCourse']);
	Route::post('classmanagement/addEnrolment', ['as' => 'addEnrolment', 'uses' => 'ClassManagementController@addEnrolment']);
	Route::post('classmanagement/deleteEnrolment', ['as' => 'deleteEnrolment', 'uses' => 'ClassManagementController@deleteEnrolment']);
	Route::post('classmanagement/addMaterial', ['as' => 'addMaterial', 'uses' => 'ClassManagementController@addMaterial']);
	Route::post('classmanagement/deleteMaterial', ['as' => 'deleteMaterial', 'uses' => 'ClassManagementController@deleteMaterial']);
	
});


Route::group(['middleware' => 'auth'], function() {
	Route::get('/', 'HomeController@index');
	Route::get('/update', 'UserController@index');
	Route::get('users/{id}', 'UserController@update');
	//For file upload and download
	
	Route::get('fileentry/get/{filename}', [
		'as'=>'getentry', 'uses'=>'FileEntryController@get']);

	Route::get('fileentry/downloadSpreadsheet/{filename}', [
  'as'=>'downloadspreadsheet', 'uses'=>'FileEntryController@getDownload']);

	Route::get('fileentry/getViewer/{filename}', [
		'as'=>'getviewer', 'uses'=>'FileEntryController@getPdfViewer']);

	Route::get('mylibrary', 'MyLibraryController@index');
	Route::get('shop', 'HomeController@shop');
	//Route::get('shop/{category_name}', 'HomeController@shop');
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
	Route::post('shoppingcart/addToLibraryOne', 'ShoppingController@addToLibraryOne');
	Route::post('shoppingcart/checkout', 'ShoppingController@checkout');
	
	Route::get('/forum/{discussionId}/{userId}',[
        'uses' => 'ForumController@liked',
        'as' => 'like'
    ]);
    
    Route::get('/forumShowTagPosts',[
        'uses' => 'ForumController@showTagPosts',
        'as' => 'forumShowTagPosts'
    ]); 
    
    Route::get('forum/sort', ['as' => 'discussionSort', 'uses' => 'ForumController@dsort']);
    
	Route::get('/forum', ['as'=>'forum', 'uses'=>'ForumController@index']);
	Route::get('/forumpage', ['as'=>'forumpage', 'uses'=>'ForumController@toPage']);
	Route::get('/forumResponsePage', ['as'=>'forumResponsePage', 'uses'=>'ForumController@showAllResponse']);
	Route::post('/createDiscussion', ['as' => 'createDiscussion', 'uses' => 'ForumController@createDiscussion']);
	Route::post('/createResponse', ['as' => 'createResponse', 'uses' => 'ForumController@createResponse']);

	//payment routes
	Route::post('/payment', ['as' => 'payment', 'uses' => 'PaymentController@index']);
	Route::post('/paymentForm', ['as' => 'paymentForm', 'uses' => 'PaymentController@paymentForm']);
	Route::get('/faq', ['as' => 'faq', 'uses' => 'FaqController@index']);

	Route::get('/transactionHistory', ['as' => 'transactionHistory', 'uses' => 'PaymentController@viewTransaction']);


	//subscribe
	Route::post('/subscribe', ['as' => 'subscribe', 'uses' => 'SubscriptionController@addSubscription']);
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


