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

Route::get('/', function () {
    return view('welcome');
});

Route::get("tests/test", "TestController@index");

Route::get("sample", "SampleController@index");

Route::get("sample/child", "SampleController@child");

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
