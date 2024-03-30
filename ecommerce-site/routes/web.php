<?php

use Illuminate\Support\Facades\Route;

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

// Home page
Route::get('/', function () {
    return view('welcome');
});

// Product routes
Route::get('/products', 'ProductController@index');
Route::get('/products/{product}', 'ProductController@show');

// Cart routes
Route::get('/cart', 'CartController@index');
Route::post('/cart', 'CartController@store');
Route::delete('/cart/{product}', 'CartController@destroy');

// Order routes
Route::get('/orders', 'OrderController@index');
Route::get('/orders/{order}', 'OrderController@show');

// User routes
Route::get('/user', 'UserController@show');
Route::get('/user/edit', 'UserController@edit');
Route::post('/user', 'UserController@update');

// Authentication routes
Auth::routes();