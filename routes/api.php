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
Route::apiResource('region', 'Api\RegionController')->middleware('cors');
Route::apiResource('tag', 'Api\TagController')->middleware('cors');
Route::apiResource('bellitalia', 'Api\BellitaliaController')->middleware('cors');
