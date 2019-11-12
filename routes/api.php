<?php

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

// Route::get('interest', 'InterestController@index');
// Route::get('interest/{id}', 'InterestController@show');
// Route::post('interest', 'InterestController@create');
// Route::put('interest/{id}', 'InterestController@update');
// Route::delete('interest/{id}', 'InterestController@destroy');

Route::apiResource('interest', 'Api\InterestController')->middleware('cors');
Route::apiResource('region', 'Api\RegionController')->middleware('cors');
Route::apiResource('tag', 'Api\TagController')->middleware('cors');
