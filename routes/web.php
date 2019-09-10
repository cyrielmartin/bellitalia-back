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
Route::get('/create', 'InterestController@create')->name('interest.create');
Route::post('/store', 'InterestController@store')->name('interest.store');
Route::get('/list', 'InterestController@getlist')->name('interest.getlist');
Route::get('interest/{interest}/edit', 'InterestController@edit')->name('interest.edit');
Route::get('interest/{interest}/delete', 'InterestController@destroy')->name('interest.destroy');
Route::get('/', 'InterestController@index')->name('interest.index');
