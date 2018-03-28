<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Api')->name('api.')->group(function() {
	Route::get('challenges/{year}/{level}', ['as' => 'challenges', 'uses' => 'ScoreApiController@challenges']);
	Route::post('scorer/save_scores', ['as' => 'save_score', 'uses' => 'ScoreApiController@save_scores']);
});
