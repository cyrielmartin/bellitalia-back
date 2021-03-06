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

Route::apiResource('interest', 'Api\InterestController')->middleware('cors');
// Route::put('interest/{id}', 'Api\InterestController@update')->middleware('cors');
// Route::get('interest', 'Api\InterestController@index')->middleware('cors');
// Route::get('interest/{id}', 'Api\InterestController@show')->middleware('cors');


Route::apiResource('region', 'Api\RegionController')->middleware('cors');
Route::apiResource('tag', 'Api\TagController')->middleware('cors');
Route::apiResource('bellitalia', 'Api\BellitaliaController')->middleware('cors');
Route::apiResource('supplement', 'Api\SupplementController')->middleware('cors');
