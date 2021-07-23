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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Product CRUD Routes
Route::post('/products', 'API\ProductController@store');
Route::get('/products', 'API\ProductController@index');
Route::get('/products/{id}', 'API\ProductController@show');
Route::put('/products/{id}', 'API\ProductController@update');
Route::post('/products/{id}', 'API\ProductController@destroy');

//Category CRUD Routes
Route::post('/categories', 'API\CategoryController@store');
Route::get('/categories', 'API\CategoryController@index');
Route::get('/categories/{id}', 'API\CategoryController@show');
Route::put('/categories/{id}', 'API\CategoryController@update');
Route::post('/categories/{id}', 'API\CategoryController@destroy');

