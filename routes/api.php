<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();


Route::post('login', 'UsersController@login');
Route::post('register', 'UsersController@register');
Route::group(['middleware' => 'auth:api'], function(){
Route::post('details', 'UsersController@details');
Route::post('photo/{userID}', 'UsersController@userPic');


//User More Details
Route::post('moredetails', 'UserdetailController@create');
Route::put('moredetails/{userID}', 'UserdetailController@update');
Route::get('moredetails/{userID}', 'UserdetailController@view');
Route::delete('moredetails/{userID}', 'UserdetailController@destroy');


//Follower

Route::post('follow', 'FollowerController@follow');
Route::post('unfollow', 'FollowerController@unfollow');
Route::get('followers/{userID}', 'FollowerController@viewFollowers');
Route::post('follow/block', 'FollowerController@blockfollowers');
Route::post('follow/unblock', 'FollowerController@unblockfollowers');

//Categories
Route::get('categories', 'CategoryController@fetchCategories');
Route::get('category/{catID}', 'CategoryController@showCategory');



//SubCategories
Route::get('subcategories', 'SubcategoryController@fetchAllSubCategories');
Route::get('subcategories/{catID}', 'SubcategoryController@fetchsubCategories');



//Posts(Users Actions)
Route::post('post/create', 'PostController@createPost');
Route::get('post/view/{postID}/{userID}', 'PostController@viewPost');
Route::get('post/like/{postID}/{userID}', 'PostController@likePost');
Route::post('post/comment', 'PostController@commentPost');
Route::get('post/comments/{postID}', 'PostController@allComments');
Route::get('fposts', 'PostController@fPost');
Route::post('fposts/img/{subID}', 'PostController@FpostImage');
Route::get('post/myposts/{userID}', 'PostController@Upost');




//Reply Comments
Route::post('post/reply', 'ReplyController@create');


});
// });
