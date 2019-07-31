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

Route::post('/create', 'InterestController@create');
Route::resource('interest', 'InterestController');
Route::get('/', 'InterestController@index');
Route::get('interest/{interest}/edit', 'InterestController@edit')->name('interest.edit');
Route::get('interest/{interest}/delete', 'InterestController@destroy')->name('interest.destroy');

// Route::resource('bellitalia', 'BellitaliaController');
// Route::resource('tag', 'TagController');
// Route::resource('interesttag', 'InterestTagController');
// Route::resource('city', 'CityController');
// Route::resource('region', 'RegionController');
