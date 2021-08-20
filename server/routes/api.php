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

//Auth
Route::post('login', 'API\AuthController@login');
Route::post('register', 'API\AuthController@register');
Route::get('logout', 'API\AuthController@logout')->middleware('jwtAuth');
Route::get('users', 'API\AuthController@getUsers')->middleware('jwtAuth');


Route::middleware(['jwtAuth'])->group(function () {
    
//Product functions Routes
Route::post('products/create', 'API\ProductController@create');
Route::get('products', 'API\ProductController@getAll');
Route::get('products/{id}/get', 'API\ProductController@get');
Route::post('products/{id}/update', 'API\ProductController@update');
Route::post('products/{id}/delete', 'API\ProductController@delete');
Route::get('products/category/{id}', 'API\ProductController@getProductByCategoryId');
Route::get('products/popular', 'API\ProductController@mostPopular');

//Category CRUD Routes
Route::post('categories/create', 'API\CategoryController@create');
Route::get('categories', 'API\CategoryController@getAll');
Route::get('categories/{id}/get', 'API\CategoryController@get');
Route::post('categories/{id}/update', 'API\CategoryController@update');
Route::post('categories/{id}/delete', 'API\CategoryController@delete');

//Tables CRUD Routes
Route::post('tables/create', 'API\TableController@create');
Route::get('tables', 'API\TableController@getAll');
Route::get('tables/{id}/get', 'API\TableController@get');
Route::post('tables/{id}/update', 'API\TableController@update');
Route::post('tables/{id}/delete', 'API\TableController@delete');

//Ticket functions
Route::post('tickets/create', 'API\TicketController@create');
Route::post('tickets/{id}/delete', 'API\TicketController@delete');
Route::post('tickets/{id}/cuenta', 'API\TicketController@cuenta');
Route::get('tickets', 'API\TicketController@getAll');
Route::get('tickets/{id}/get/', 'API\TicketController@get');
Route::post('tickets/{id}/changeTable/', 'API\TicketController@changeTable');

//Order function
Route::post('orders/create', 'API\OrderController@create');
Route::get('orders', 'API\OrderController@getAll');
Route::post('orders/{id}/delete', 'API\OrderController@delete');

//ProductOrder function
Route::post('productOrders/create', 'API\ProductOrderController@create');


});
