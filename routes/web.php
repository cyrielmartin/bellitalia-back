<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });
//
// Route::get('/admin', function() {
//     return view('admin.main');
// });

// Route::resource('place', 'PlaceController');
//
// Route::get('/', 'PlaceController@index');
// Route::get('/admin/create', 'PlaceController@create');

Route::post('/create', 'InterestController@create');
Route::resource('interest', 'InterestController');
Route::get('/', 'InterestController@index');
Route::get('interest/{interest}/edit', 'InterestController@edit');
Route::get('interest/{interest}/delete', 'InterestController@destroy');

// Route::resource('bellitalia', 'BellitaliaController');
// Route::resource('tag', 'TagController');
// Route::resource('interesttag', 'InterestTagController');
// Route::resource('city', 'CityController');
// Route::resource('region', 'RegionController');
